<?php

namespace Notion\Views\Query;

use DateTimeImmutable;

/**
 * Cached result set of a view query. Results expire after `expiresAt`.
 *
 * @psalm-type ViewQueryJson = array{
 *      object: "view_query",
 *      id: string,
 *      view_id: string,
 *      expires_at: string,
 *      total_count: int|float,
 *      results: list<array{ object: "page", id: string }>,
 *      next_cursor: string|null,
 *      has_more: bool,
 *      request_status?: array{ type: "complete"|"incomplete", incomplete_reason?: string },
 * }
 *
 * @psalm-immutable
 */
final readonly class ViewQuery
{
    /** @param string[] $pageIds Page IDs of the first page of results */
    private function __construct(
        public string $id,
        public string $viewId,
        public DateTimeImmutable $expiresAt,
        public int $totalCount,
        public array $pageIds,
        public string|null $nextCursor,
        public bool $hasMore,
        public bool $isIncomplete,
    ) {
    }

    /**
     * @param ViewQueryJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        return new self(
            $array["id"],
            $array["view_id"],
            new DateTimeImmutable($array["expires_at"]),
            (int) $array["total_count"],
            array_map(fn (array $page): string => $page["id"], $array["results"]),
            $array["next_cursor"],
            $array["has_more"],
            ($array["request_status"]["type"] ?? "complete") === "incomplete",
        );
    }
}
