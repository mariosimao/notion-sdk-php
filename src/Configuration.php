<?php

namespace Notion;

use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;

/**
 * Notion SDK configuration
 *
 * @psalm-type ConfigProperties = array{
 *     token: string,
 *     version: string,
 *     httpClient: ClientInterface,
 *     requestFactory: RequestFactoryInterface,
 *     retryOnConflict: bool,
 *     retryOnConflictAttempts: int,
 *     retryOnRateLimit: bool,
 *     retryOnRateLimitAttempts: int,
 *     retryOnRateLimitMaxWait: int,
 *     ...
 * }
 *
 * @psalm-immutable
 */
final readonly class Configuration
{
    private function __construct(
        public string $token,
        public string $version,
        public ClientInterface $httpClient,
        public RequestFactoryInterface $requestFactory,
        public bool $retryOnConflict,
        public int $retryOnConflictAttempts,
        public bool $retryOnRateLimit,
        public int $retryOnRateLimitAttempts,
        public int $retryOnRateLimitMaxWait,
    ) {
    }

    public static function create(string $token): self
    {
        return new self(
            token: $token,
            version: Notion::API_VERSION,
            httpClient: Psr18ClientDiscovery::find(),
            requestFactory: Psr17FactoryDiscovery::findRequestFactory(),
            retryOnConflict: true,
            retryOnConflictAttempts: 3,
            retryOnRateLimit: true,
            retryOnRateLimitAttempts: 3,
            retryOnRateLimitMaxWait: 60,
        );
    }

    public static function createFromPsrImplementations(
        string $token,
        ClientInterface $httpClient,
        RequestFactoryInterface $requestFactory,
    ): self {
        return new self(
            token: $token,
            version: Notion::API_VERSION,
            httpClient: $httpClient,
            requestFactory: $requestFactory,
            retryOnConflict: true,
            retryOnConflictAttempts: 3,
            retryOnRateLimit: true,
            retryOnRateLimitAttempts: 3,
            retryOnRateLimitMaxWait: 60,
        );
    }

    /**
     * Retry operations when the Notion API responds with conflict error.
     *
     * @param int $attempts Number of attempts
     */
    public function enableRetryOnConflict(int $attempts = 1): self
    {
        $properties = $this->properties();
        $properties["retryOnConflict"] = true;
        $properties["retryOnConflictAttempts"] = $attempts;

        return new self(...$properties);
    }

    public function disableRetryOnConflict(): self
    {
        $properties = $this->properties();
        $properties["retryOnConflict"] = false;
        $properties["retryOnConflictAttempts"] = 0;

        return new self(...$properties);
    }

    /**
     * Retry operations when the Notion API responds with a rate limit error.
     *
     * The SDK waits the number of seconds sent by Notion in
     * `additional_data.retry_after` (or the `Retry-After` header) before
     * retrying. Requests blocked by Notion (`public_api_request_blocked`) are
     * never retried.
     *
     * @param int $attempts Maximum number of retries
     * @param int $maxWait Maximum number of seconds to wait before each retry
     */
    public function enableRetryOnRateLimit(int $attempts = 3, int $maxWait = 60): self
    {
        $properties = $this->properties();
        $properties["retryOnRateLimit"] = true;
        $properties["retryOnRateLimitAttempts"] = $attempts;
        $properties["retryOnRateLimitMaxWait"] = $maxWait;

        return new self(...$properties);
    }

    public function disableRetryOnRateLimit(): self
    {
        $properties = $this->properties();
        $properties["retryOnRateLimit"] = false;
        $properties["retryOnRateLimitAttempts"] = 0;

        return new self(...$properties);
    }

    /** @psalm-return ConfigProperties */
    private function properties(): array
    {
        return get_object_vars($this);
    }
}
