<?php

namespace Notion\Users;

use Notion\NotionException;
use Nyholm\Psr7\Request;
use Psr\Http\Client\ClientInterface;

/** @psalm-import-type UserJson from User */
class Client
{
    private ClientInterface $psrClient;
    private string $token;
    private string $version;

    public function __construct(
        ClientInterface $psrClient,
        string $token,
        string $version
    ) {
        $this->psrClient = $psrClient;
        $this->token = $token;
        $this->version = $version;
    }

    public function find(string $userId): User
    {
        $request = new Request(
            "GET",
            "https://api.notion.com/v1/users/{$userId}",
            [
                "Authorization"  => "Bearer {$this->token}",
                "Notion-Version" => $this->version,
            ]
        );

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
        $request = new Request(
            "GET",
            "https://api.notion.com/v1/users",
            [
                "Authorization"  => "Bearer {$this->token}",
                "Notion-Version" => $this->version,
            ]
        );

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
        $request = new Request(
            "GET",
            "https://api.notion.com/v1/users/me",
            [
                "Authorization"  => "Bearer {$this->token}",
                "Notion-Version" => $this->version,
            ]
        );

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
