<?php

namespace Notion\Users;

use Notion\Configuration;
use Notion\Infrastructure\Http;

/** @psalm-import-type UserJson from User */
class Client
{
    /**
     * @internal Use `\Notion\Notion::pages()` instead
     */
    public function __construct(
        private readonly Configuration $config,
    ) {
    }

    public function find(string $userId): User
    {
        $url = "https://api.notion.com/v1/users/{$userId}";
        $request = Http::createRequest($url, $this->config);

        /** @psalm-var UserJson $body */
        $body = Http::sendRequest($request, $this->config);

        return User::fromArray($body);
    }

    /**
     * @return User[]
     */
    public function findAll(): array
    {
        $url = "https://api.notion.com/v1/users";
        $request = Http::createRequest($url, $this->config);

        /** @var array{ results: UserJson[] } $body */
        $body = Http::sendRequest($request, $this->config);

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
        $request = Http::createRequest($url, $this->config);

        /** @psalm-var UserJson $body */
        $body = Http::sendRequest($request, $this->config);

        return User::fromArray($body);
    }
}
