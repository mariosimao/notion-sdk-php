<?php

namespace Notion;

use GuzzleHttp\Client as GuzzleClient;
use Notion\Users\Client as UsersClient;
use Psr\Http\Client\ClientInterface;
use Symfony\Component\HttpClient\Psr18Client as SymfonyClient;

class Client
{
    public const NOTION_VERSION = "2021-08-16";

    private ClientInterface $psrClient;
    private string $token;

    private function __construct(ClientInterface $psrClient, string $token) {
        $this->token = $token;
        $this->psrClient = $psrClient;
    }

    public static function create(string $token): self
    {
        $psrClient = self::resolvePsrClient();

        return new self($psrClient, $token);
    }

    public static function createWithPsrClient(
        ClientInterface $psrClient,
        string $token,
    ) {
        return new self($psrClient, $token);
    }

    public function users(): UsersClient
    {
        return new UsersClient(
            $this->psrClient,
            $this->token,
            self::NOTION_VERSION
        );
    }

    private static function resolvePsrClient(): ClientInterface
    {
        if (class_exists(GuzzleClient::class)) {
            return new GuzzleClient();
        }

        if (class_exists(SymfonyClient::class)) {
            return new SymfonyClient();
        }

        throw new \Exception(
            "You cannot use 'Notion\\Client' as no PSR-18 has been found.
            Try running 'composer require guzzlehttp/guzzle"
        );
    }
}
