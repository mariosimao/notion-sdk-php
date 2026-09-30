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
 *     retryPolicy: RetryPolicy,
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
        public RetryPolicy $retryPolicy,
    ) {
    }

    public static function create(string $token): self
    {
        return new self(
            token: $token,
            version: Notion::API_VERSION,
            httpClient: Psr18ClientDiscovery::find(),
            requestFactory: Psr17FactoryDiscovery::findRequestFactory(),
            retryPolicy: RetryPolicy::create(),
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
            retryPolicy: RetryPolicy::create(),
        );
    }

    public function withRetryPolicy(RetryPolicy $retryPolicy): self
    {
        $properties = $this->properties();
        $properties["retryPolicy"] = $retryPolicy;

        return new self(...$properties);
    }

    public function withoutRetries(): self
    {
        return $this->withRetryPolicy(RetryPolicy::none());
    }

    /** @psalm-return ConfigProperties */
    private function properties(): array
    {
        return get_object_vars($this);
    }
}
