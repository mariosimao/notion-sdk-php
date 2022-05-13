<?php

namespace Notion\Databases\Query;

use Notion\Pages\Page;

/**
 * Database query result
 *
 * @psalm-type QueryResultJson = array{
 *      results: list<PageJson>,
 *      has_more: bool,
 *      next_cursor: string|null
 * }
 *
 * @psalm-import-type PageJson from \Notion\Pages\Page
 * @psalm-immutable
 */
class Result
{
    /** @var list<Page> $page */
    private array $pages;
    private bool $hasMore;
    private string|null $nextCursor;

    /** @param list<Page> $pages */
    private function __construct(array $pages, bool $hasMore, string|null $nextCursor)
    {
        $this->pages = $pages;
        $this->hasMore = $hasMore;
        $this->nextCursor = $nextCursor;
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

    /** @return list<Page> */
    public function pages(): array
    {
        return $this->pages;
    }

    public function hasMore(): bool
    {
        return $this->hasMore;
    }

    public function nextCursor(): string|null
    {
        return $this->nextCursor;
    }
}
