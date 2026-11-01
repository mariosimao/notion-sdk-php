<?php

namespace Notion\Users;

/**
 * A single page of users returned by the Notion API.
 *
 * @psalm-import-type UserJson from User
 *
 * @psalm-type UserListJson = array{
 *      results: list<UserJson>,
 *      next_cursor: string|null,
 *      has_more: bool,
 *      ...
 * }
 *
 * @psalm-immutable
 */
final readonly class UserList
{
    /** @param list<User> $results */
    private function __construct(
        public array $results,
        public string|null $nextCursor,
        public bool $hasMore,
    ) {
    }

    /** @param list<User> $results */
    public static function create(array $results, string|null $nextCursor = null, bool $hasMore = false): self
    {
        return new self($results, $nextCursor, $hasMore);
    }

    /**
     * @psalm-param UserListJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        $results = array_map(
            fn(array $user): User => User::fromArray($user),
            $array["results"],
        );

        return new self($results, $array["next_cursor"], $array["has_more"]);
    }
}
