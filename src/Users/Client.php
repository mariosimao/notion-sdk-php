<?php

namespace Notion\Users;

use Notion\Configuration;
use Notion\Infrastructure\Http;
use Notion\Infrastructure\Pagination;

/**
 * @psalm-import-type UserJson from User
 * @psalm-import-type UserListJson from UserList
 */
final readonly class Client
{
    /**
     * @internal Use `\Notion\Notion::pages()` instead
     */
    public function __construct(
        private Configuration $config,
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
     * Retrieve a single page of the workspace users
     *
     * @param string|null $startCursor Cursor returned as `nextCursor` by a previous call
     * @param int|null $pageSize Number of users to return (maximum: 100)
     */
    public function list(string|null $startCursor = null, int|null $pageSize = null): UserList
    {
        $url = Pagination::url("https://api.notion.com/v1/users", $startCursor, $pageSize);
        $request = Http::createRequest($url, $this->config);

        /** @psalm-var UserListJson $body */
        $body = Http::sendRequest($request, $this->config);

        return UserList::fromArray($body);
    }

    /**
     * Retrieve all workspace users, following pagination
     *
     * @return list<User>
     */
    public function findAll(): array
    {
        $users = [];
        $startCursor = null;

        do {
            $list = $this->list($startCursor, Pagination::MAX_PAGE_SIZE);
            $users = [ ...$users, ...$list->results ];
            $startCursor = $list->nextCursor;
        } while ($list->hasMore && $startCursor !== null);

        return $users;
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
