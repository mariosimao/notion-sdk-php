<?php

namespace Notion\Databases\Query;

use Notion\Pages\Page;

/**
 * Database query result
 *
 * @psalm-type QueryResultJson = array{
 *      results: PageJson[],
 *      has_more: bool,
 *      next_cursor: string|null
 * }
 *
 * @psalm-import-type PageJson from \Notion\Pages\Page
 * @psalm-immutable
 */
class Result
{
    /** @param Page[] $pages */
    private function __construct(
        public readonly array $pages,
        public readonly bool $hasMore,
        public readonly string|null $nextCursor
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

        return new self($pages, $array["has_more"], $array["next_cursor"]);
    }
}
