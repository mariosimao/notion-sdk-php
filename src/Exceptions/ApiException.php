<?php

namespace Notion\Exceptions;

use DateTimeImmutable;
use Psr\Http\Message\ResponseInterface;

/**
 * Exception from Notion API
 */
class ApiException extends NotionException
{
    public readonly string $notionCode;
    public readonly ResponseInterface $response;
    public readonly ErrorAdditionalData $additionalData;

    final public function __construct(
        string $message,
        string $notionCode,
        ResponseInterface $response,
        ErrorAdditionalData|null $additionalData = null,
    ) {
        $this->notionCode = $notionCode;
        $this->response = $response;
        $this->additionalData = $additionalData ?? ErrorAdditionalData::empty();

        parent::__construct($message);
    }

    final public static function fromResponse(ResponseInterface $response): static
    {
        /** @var array{ message?: string, code?: string, additional_data?: array|null }|false|null $body */
        $body = json_decode((string) $response->getBody(), true);

        if (!is_array($body)) {
            return new static("", "", $response);
        }

        $code = $body["code"] ?? "";
        $message = $body["message"] ?? "";
        $additionalData = isset($body["additional_data"])
            ? ErrorAdditionalData::fromArray($body["additional_data"])
            : ErrorAdditionalData::empty();

        return match ($code) {
            "conflict_error" => new ConflictException($message, $code, $response, $additionalData),
            "rate_limited"   => new RateLimitedException($message, $code, $response, $additionalData),
            default          => new static($message, $code, $response, $additionalData),
        };
    }

    /**
     * Number of seconds to wait before retrying the request.
     *
     * Uses `additional_data.retry_after` from the response body and falls
     * back to the `Retry-After` response header. Returns `null` when neither
     * is available.
     */
    public function retryAfter(): int|null
    {
        return $this->additionalData->retryAfter ?? self::retryAfterFromHeader($this->response);
    }

    private static function retryAfterFromHeader(ResponseInterface $response): int|null
    {
        $header = trim($response->getHeaderLine("Retry-After"));
        if ($header === "") {
            return null;
        }

        $seconds = ErrorAdditionalData::parseSeconds($header);
        if ($seconds !== null) {
            return $seconds;
        }

        $date = DateTimeImmutable::createFromFormat(DATE_RFC7231, $header);
        if ($date === false) {
            return null;
        }

        return max(0, $date->getTimestamp() - time());
    }
}
