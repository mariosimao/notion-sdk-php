<?php

namespace Notion\Sessions\Events;

/**
 * @psalm-type EventQueryResultJson = array{
 *      object: "list",
 *      results: list<array>,
 *      next_cursor: string|null,
 *      has_more: bool,
 * }
 *
 * @psalm-immutable
 */
final readonly class EventQueryResult
{
    /** @param list<SessionEvent> $events */
    private function __construct(
        public array $events,
        public string|null $nextCursor,
        public bool $hasMore,
    ) {
    }

    /**
     * @param EventQueryResultJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        return new self(
            EventFactory::fromArrayList($array["results"]),
            $array["next_cursor"],
            $array["has_more"],
        );
    }
}
