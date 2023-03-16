<?php

namespace Notion\Search;

use Notion\Databases\Database;
use Notion\Pages\Page;

/**
 * @psalm-immutable
 *
 * @psalm-import-type PageJson from \Notion\Pages\Page
 * @psalm-import-type DatabaseJson from \Notion\Databases\Database
 *
 * @psalm-type ResultJson = array{
 *      object: string,
 *      results: list{PageJson, DatabaseJson},
 *      next_cursor: string|null,
 *      has_more: bool
 * }
 */
class Result
{
    /** @param list{Page, Database} $results */
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

            if ($result["object"] === "database") {
                /** @psalm-var DatabaseJson $result */
                $results[] = Database::fromArray($result);
            }
        }

        /** @psalm-var list{Page, Database} $results */
        return new self(
            $results,
            $array["next_cursor"],
            $array["has_more"],
        );
    }
}
