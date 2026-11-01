<?php

namespace Notion\Sessions;

/**
 * @psalm-import-type SessionJson from Session
 *
 * @psalm-type QueryResultJson = array{
 *      object: "list",
 *      results: list<SessionJson>,
 *      next_cursor: string|null,
 *      has_more: bool,
 * }
 *
 * @psalm-immutable
 */
final readonly class QueryResult
{
    /**
     * Access filtering can leave `$sessions` empty while `$hasMore` is `true`.
     * Keep paginating with `$nextCursor` until `$hasMore` is `false`.
     *
     * @param list<Session> $sessions
     */
    private function __construct(
        public array $sessions,
        public string|null $nextCursor,
        public bool $hasMore,
    ) {
    }

    /**
     * @param QueryResultJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        return new self(
            array_map(Session::fromArray(...), $array["results"]),
            $array["next_cursor"],
            $array["has_more"],
        );
    }
}
