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
        $allUsers = [];
        $startCursor = null;

        do {
            $queryParams = $startCursor ? ['start_cursor' => $startCursor] : [];
            $requestUrl = $url . ($startCursor ? '?' . http_build_query($queryParams) : '');

            $request = Http::createRequest($requestUrl, $this->config);

            /** @var array{ results: UserJson[], has_more: bool, next_cursor: ?string } $body */
            $body = Http::sendRequest($request, $this->config);

            $allUsers = array_merge(
                $allUsers,
                array_map(
                    function (array $userData): User {
                        return User::fromArray($userData);
                    },
                    $body["results"]
                )
            );

            $startCursor = $body['next_cursor'] ?? null;
        } while (!empty($body['has_more']));

        return $allUsers;
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
