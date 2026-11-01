<?php

namespace Notion\DataSources\Query;

use Notion\Pages\Page;

/**
 * Data source query result
 *
 * @psalm-type QueryResultJson = array{
 *      results: PageJson[],
 *      has_more: bool,
 *      next_cursor: string|null,
 *      request_status?: RequestStatusJson|null
 * }
 *
 * @psalm-import-type PageJson from \Notion\Pages\Page
 * @psalm-import-type RequestStatusJson from RequestStatus
 * @psalm-immutable
 */
final readonly class Result
{
    /** @param Page[] $pages */
    private function __construct(
        public array $pages,
        public bool $hasMore,
        public string|null $nextCursor,
        public RequestStatus|null $requestStatus,
    ) {
    }

    /** @param QueryResultJson $array */
    public static function fromArray(array $array): self
    {
        $pages = array_map(
            function (array $pageArray): Page {
                return Page::fromArray($pageArray);
            },
            $array["results"],
        );

        $requestStatus = isset($array["request_status"])
            ? RequestStatus::fromArray($array["request_status"])
            : null;

        return new self($pages, $array["has_more"], $array["next_cursor"], $requestStatus);
    }

    /**
     * Whether the query was cut off by the per-query result limit
     *
     * When `true`, following `nextCursor` will not reach every matching row.
     * Use `Notion\DataSources\Client::iterateAll()` or `queryAll()` to read
     * all rows.
     */
    public function isIncomplete(): bool
    {
        return $this->requestStatus?->isIncomplete() ?? false;
    }
}
