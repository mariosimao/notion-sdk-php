<?php

namespace Notion;

use DateTimeImmutable;
use Notion\Exceptions\ApiException;
use Notion\Exceptions\ConflictException;
use Notion\Exceptions\RetryPolicyException;
use Psr\Http\Message\ResponseInterface;

/**
 * Retry policy for transient Notion API errors
 *
 * Rate limited (429), overloaded (529) and conflict responses are retried for
 * every HTTP method. Server errors (500, 502, 503 and 504) are only retried for
 * idempotent methods (GET and DELETE) to avoid repeating writes.
 */
final readonly class RetryPolicy
{
    public const DEFAULT_MAX_RETRIES = 2;
    public const DEFAULT_INITIAL_DELAY_MS = 1000;
    public const DEFAULT_MAX_DELAY_MS = 60000;

    private const RETRYABLE_SERVER_ERRORS = [500, 502, 503, 504];
    private const IDEMPOTENT_METHODS = ["GET", "DELETE"];

    /** @psalm-mutation-free */
    private function __construct(
        public int $maxRetries,
        public int $initialDelayMs,
        public int $maxDelayMs,
    ) {
    }

    /**
     * @param int $maxRetries Maximum number of retries after the first attempt
     * @param int $initialDelayMs Delay before the first retry, doubled on each retry
     * @param int $maxDelayMs Upper bound for any delay, including `Retry-After`
     *
     * @psalm-pure
     */
    public static function create(
        int $maxRetries = self::DEFAULT_MAX_RETRIES,
        int $initialDelayMs = self::DEFAULT_INITIAL_DELAY_MS,
        int $maxDelayMs = self::DEFAULT_MAX_DELAY_MS,
    ): self {
        if ($maxRetries < 0) {
            throw RetryPolicyException::negativeMaxRetries();
        }

        if ($initialDelayMs < 0) {
            throw RetryPolicyException::negativeInitialDelay();
        }

        if ($maxDelayMs < $initialDelayMs) {
            throw RetryPolicyException::maxDelayLowerThanInitialDelay();
        }

        return new self($maxRetries, $initialDelayMs, $maxDelayMs);
    }

    /** @psalm-pure */
    public static function none(): self
    {
        return new self(0, 0, 0);
    }

    public function shouldRetry(string $method, ApiException $exception, int $retries): bool
    {
        if ($retries >= $this->maxRetries) {
            return false;
        }

        if ($exception instanceof ConflictException) {
            return true;
        }

        $status = $exception->response->getStatusCode();

        if ($status === 429) {
            return self::rateLimitReason($exception->response) !== "public_api_request_blocked";
        }

        if ($status === 529) {
            return true;
        }

        return in_array($status, self::RETRYABLE_SERVER_ERRORS, true)
            && in_array(strtoupper($method), self::IDEMPOTENT_METHODS, true);
    }

    /** Milliseconds to wait before the next retry. */
    public function delayMs(ResponseInterface $response, int $retries): int
    {
        $baseDelay = self::retryAfterMs($response)
            ?? min($this->maxDelayMs, $this->initialDelayMs * 2 ** $retries);

        $jitter = random_int(0, intdiv($baseDelay, 4));

        return min($this->maxDelayMs, $baseDelay + $jitter);
    }

    private static function retryAfterMs(ResponseInterface $response): int|null
    {
        $retryAfter = trim($response->getHeaderLine("Retry-After"));

        if ($retryAfter === "") {
            return null;
        }

        if (ctype_digit($retryAfter)) {
            return (int) $retryAfter * 1000;
        }

        $date = DateTimeImmutable::createFromFormat(DATE_RFC7231, $retryAfter);
        if ($date === false) {
            return null;
        }

        return max(0, ($date->getTimestamp() - time()) * 1000);
    }

    private static function rateLimitReason(ResponseInterface $response): string|null
    {
        /** @var mixed $body */
        $body = json_decode((string) $response->getBody(), true);

        if (!is_array($body) || !is_array($body["additional_data"] ?? null)) {
            return null;
        }

        /** @var mixed $reason */
        $reason = $body["additional_data"]["rate_limit_reason"] ?? null;

        return is_string($reason) ? $reason : null;
    }
}
