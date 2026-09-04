<?php

namespace Notion\Search;

use Notion\DataSources\DataSource;
use Notion\Pages\Page;

/**
 * @psalm-immutable
 *
 * @psalm-import-type PageJson from \Notion\Pages\Page
 * @psalm-import-type DataSourceJson from \Notion\DataSources\DataSource
 *
 * @psalm-type ResultJson = array{
 *      object: string,
 *      results: array<int, PageJson|DataSourceJson>,
 *      next_cursor: string|null,
 *      has_more: bool
 * }
 */
class Result
{
    /** @psalm-param array<int, Page|DataSource> $results */
    private function __construct(
        public readonly array $results,
        public readonly string|null $nextCursor,
        public readonly bool $hasMore,
    ) {
    }

    /**
     * @psalm-param ResultJson $array
     */
    public static function fromArray(array $array): self
    {
        $results = [];
        foreach ($array["results"] as $result) {
            if ($result["object"] === "page") {
                /** @psalm-var PageJson $result */
                $results[] = Page::fromArray($result);
            }

            if ($result["object"] === "data_source") {
                /** @psalm-var DataSourceJson $result */
                $results[] = DataSource::fromArray($result);
            }
        }

        /** @psalm-var array<int, Page|DataSource> $results */
        return new self(
            $results,
            $array["next_cursor"],
            $array["has_more"],
        );
    }
}
