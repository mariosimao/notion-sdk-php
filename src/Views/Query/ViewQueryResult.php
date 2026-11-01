<?php

namespace Notion\Views\Query;

/**
 * Page of results of a view query
 *
 * @psalm-type ViewQueryResultJson = array{
 *      results: list<array{ object: "page", id: string }>,
 *      next_cursor: string|null,
 *      has_more: bool,
 *      request_status?: array{ type: "complete"|"incomplete", incomplete_reason?: string },
 * }
 *
 * @psalm-immutable
 */
final readonly class ViewQueryResult
{
    /** @param string[] $pageIds */
    private function __construct(
        public array $pageIds,
        public string|null $nextCursor,
        public bool $hasMore,
        public bool $isIncomplete,
    ) {
    }

    /**
     * @param ViewQueryResultJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        return new self(
            array_map(fn (array $page): string => $page["id"], $array["results"]),
            $array["next_cursor"],
            $array["has_more"],
            ($array["request_status"]["type"] ?? "complete") === "incomplete",
        );
    }
}
