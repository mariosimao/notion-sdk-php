<?php

namespace Notion\DataSources\Query;

use Notion\Common\RequestStatus;
use Notion\Pages\Page;

/**
 * Data source query result
 *
 * @psalm-type QueryResultJson = array{
 *      results: PageJson[],
 *      has_more: bool,
 *      next_cursor: string|null,
 *      request_status?: RequestStatusJson
 * }
 *
 * @psalm-import-type PageJson from \Notion\Pages\Page
 * @psalm-import-type RequestStatusJson from \Notion\Common\RequestStatus
 * @psalm-immutable
 */
final readonly class Result
{
    /** @param Page[] $pages */
    private function __construct(
        public array $pages,
        public bool $hasMore,
        public string|null $nextCursor,
        public RequestStatus $requestStatus,
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
            : RequestStatus::complete();

        return new self($pages, $array["has_more"], $array["next_cursor"], $requestStatus);
    }

    /**
     * Whether Notion stopped returning results before the query was exhausted
     *
     * When `true`, `hasMore` is `false` even though more matching entries exist.
     */
    public function isIncomplete(): bool
    {
        return $this->requestStatus->isIncomplete();
    }
}
