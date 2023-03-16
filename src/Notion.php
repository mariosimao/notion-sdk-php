<?php

namespace Notion;

use Notion\Blocks\Client as BlocksClient;
use Notion\Comments\Client as CommentsClient;
use Notion\Databases\Client as DatabasesClient;
use Notion\Pages\Client as PagesClient;
use Notion\Search\Client as SearchClient;
use Notion\Users\Client as UsersClient;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;

class Notion
{
    public const API_VERSION = "2022-06-28";

    private function __construct(
        private readonly Configuration $configuration,
    ) {
    }

    public static function create(string $token): self
    {
        $configuration = Configuration::create($token);

        return new self($configuration);
    }

    public static function createFromConfig(Configuration $config): self
    {
        return new self($config);
    }

    public static function createWithPsrImplementations(
        ClientInterface $psrClient,
        RequestFactoryInterface $requestFactory,
        string $token,
    ): self {
        $configuration = Configuration::createFromPsrImplementations(
            $token,
            $psrClient,
            $requestFactory,
        );

        return new self($configuration);
    }

    public function users(): UsersClient
    {
        return new UsersClient($this->configuration);
    }

    public function pages(): PagesClient
    {
        return new PagesClient($this->configuration);
    }

    public function databases(): DatabasesClient
    {
        return new DatabasesClient($this->configuration);
    }

    public function blocks(): BlocksClient
    {
        return new BlocksClient($this->configuration);
    }

    public function comments(): CommentsClient
    {
        return new CommentsClient($this->configuration);
    }

    public function search(): SearchClient
    {
        return new SearchClient($this->configuration);
    }
}
