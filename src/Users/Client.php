<?php

namespace Notion\Users;

use Notion\Infrastructure\Http;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;

/** @psalm-import-type UserJson from User */
class Client
{
    private ClientInterface $psrClient;
    private RequestFactoryInterface $requestFactory;
    private string $token;
    private string $version;

    /**
     * @internal Use `\Notion\Notion::pages()` instead
     */
    public function __construct(
        ClientInterface $psrClient,
        RequestFactoryInterface $requestFactory,
        string $token,
        string $version,
    ) {
        $this->psrClient = $psrClient;
        $this->requestFactory = $requestFactory;
        $this->token = $token;
        $this->version = $version;
    }

    public function find(string $userId): User
    {
        $url = "https://api.notion.com/v1/users/{$userId}";
        $request = Http::createRequest($this->requestFactory, $this->version, $this->token, $url);

        $response = $this->psrClient->sendRequest($request);

        /** @psalm-var UserJson $body */
        $body = Http::parseBody($response);

        return User::fromArray($body);
    }

    /**
     * @return User[]
     */
    public function findAll(): array
    {
        $url = "https://api.notion.com/v1/users";
        $request = Http::createRequest($this->requestFactory, $this->version, $this->token, $url);

        $response = $this->psrClient->sendRequest($request);

        /** @var array{ results: UserJson[] } $body */
        $body = Http::parseBody($response);

        return array_map(
            function (array $userData): User {
                return User::fromArray($userData);
            },
            $body["results"],
        );
    }

    public function me(): User
    {
        $url = "https://api.notion.com/v1/users/me";
        $request = Http::createRequest($this->requestFactory, $this->version, $this->token, $url);

        $response = $this->psrClient->sendRequest($request);

        /** @psalm-var UserJson $body */
        $body = Http::parseBody($response);

        return User::fromArray($body);
    }
}
