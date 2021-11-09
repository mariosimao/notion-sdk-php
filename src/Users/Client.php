<?php

namespace Notion\Users;

use Notion\NotionException;
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
        $request = $this->requestFactory->createRequest("GET", $url)
            ->withHeader("Authorization", "Bearer {$this->token}")
            ->withHeader("Notion-Version", $this->version);

        $response = $this->psrClient->sendRequest($request);

        /** @var array */
        $body = json_decode((string) $response->getBody(), true);

        if ($response->getStatusCode() !== 200) {
            /** @var array{ message: string, code: string} $body */
            $message = $body["message"];
            $code = $body["code"];

            throw new NotionException($message, $code);
        }

        /** @psalm-var UserJson $body */
        return User::fromArray($body);
    }

    /**
     * @return User[]
     */
    public function findAll(): array
    {
        $url = "https://api.notion.com/v1/users";
        $request = $this->requestFactory->createRequest("GET", $url)
            ->withHeader("Authorization", "Bearer {$this->token}")
            ->withHeader("Notion-Version", $this->version);

        $response = $this->psrClient->sendRequest($request);
        /** @var array $body */
        $body = json_decode((string) $response->getBody(), true);

        if ($response->getStatusCode() !== 200) {
            /** @var array{ message: string, code: string} $body */
            $message = $body["message"];
            $code = $body["code"];

            throw new NotionException($message, $code);
        }

        /** @var array{ results: UserJson[] } $body */
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
        $request = $this->requestFactory->createRequest("GET", $url)
            ->withHeader("Authorization", "Bearer {$this->token}")
            ->withHeader("Notion-Version", $this->version);

        $response = $this->psrClient->sendRequest($request);
        /** @var array $body */
        $body = json_decode((string) $response->getBody(), true);

        if ($response->getStatusCode() !== 200) {
            /** @var array{ message: string, code: string } $body */
            $message = $body["message"];
            $code = $body["code"];

            throw new NotionException($message, $code);
        }

        /** @psalm-var UserJson $body */
        return User::fromArray($body);
    }
}
