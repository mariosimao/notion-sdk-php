<?php

namespace Notion;

use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use Notion\Blocks\Client as BlocksClient;
use Notion\Databases\Client as DatabasesClient;
use Notion\Pages\Client as PagesClient;
use Notion\Users\Client as UsersClient;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;

class Notion
{
    public const API_VERSION = "2022-06-28";

    private function __construct(
        private readonly ClientInterface $psrClient,
        private readonly RequestFactoryInterface $requestFactory,
        private readonly string $token,
    ) {
    }

    public static function create(string $token): self
    {
        $psrClient = Psr18ClientDiscovery::find();
        $requestFactory = Psr17FactoryDiscovery::findRequestFactory();

        return new self($psrClient, $requestFactory, $token);
    }

    public static function createWithPsrImplementations(
        ClientInterface $psrClient,
        RequestFactoryInterface $requestFactory,
        string $token,
    ): self {
        return new self($psrClient, $requestFactory, $token);
    }

    public function users(): UsersClient
    {
        return new UsersClient(
            $this->psrClient,
            $this->requestFactory,
            $this->token,
            self::API_VERSION
        );
    }

    public function pages(): PagesClient
    {
        return new PagesClient(
            $this->psrClient,
            $this->requestFactory,
            $this->token,
            self::API_VERSION
        );
    }

    public function databases(): DatabasesClient
    {
        return new DatabasesClient(
            $this->psrClient,
            $this->requestFactory,
            $this->token,
            self::API_VERSION,
        );
    }

    public function blocks(): BlocksClient
    {
        return new BlocksClient(
            $this->psrClient,
            $this->requestFactory,
            $this->token,
            self::API_VERSION,
        );
    }
}
