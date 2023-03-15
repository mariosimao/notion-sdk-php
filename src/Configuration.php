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
 *     ...
 * }
 *
 * @psalm-immutable
 */
class Configuration
{
    private function __construct(
        public readonly string $token,
        public readonly string $version,
        public readonly ClientInterface $httpClient,
        public readonly RequestFactoryInterface $requestFactory,
        public readonly bool $retryOnConflict,
        public readonly int $retryOnConflictAttempts,
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

    /** @psalm-return ConfigProperties */
    private function properties(): array
    {
        return get_object_vars($this);
    }
}
