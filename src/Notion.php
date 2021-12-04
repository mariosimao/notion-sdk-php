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
    public const NOTION_VERSION = "2021-08-16";

    private ClientInterface $psrClient;
    private RequestFactoryInterface $requestFactory;
    private string $token;

    private function __construct(
        ClientInterface $psrClient,
        RequestFactoryInterface $requestFactory,
        string $token,
    ) {
        $this->psrClient = $psrClient;
        $this->requestFactory = $requestFactory;
        $this->token = $token;
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
            self::NOTION_VERSION
        );
    }

    public function pages(): PagesClient
    {
        return new PagesClient(
            $this->psrClient,
            $this->requestFactory,
            $this->token,
            self::NOTION_VERSION
        );
    }

    public function databases(): DatabasesClient
    {
        return new DatabasesClient(
            $this->psrClient,
            $this->requestFactory,
            $this->token,
            self::NOTION_VERSION,
        );
    }

    public function blocks(): BlocksClient
    {
        return new BlocksClient(
            $this->psrClient,
            $this->requestFactory,
            $this->token,
            self::NOTION_VERSION,
        );
    }
}
