<?php

namespace Notion\Views;

/**
 * Page of view IDs returned when listing views
 *
 * @psalm-type ViewListJson = array{
 *      results: list<array{ object: "view", id: string }>,
 *      next_cursor: string|null,
 *      has_more: bool,
 * }
 *
 * @psalm-immutable
 */
final readonly class ViewList
{
    /** @param string[] $viewIds */
    private function __construct(
        public array $viewIds,
        public string|null $nextCursor,
        public bool $hasMore,
    ) {
    }

    /**
     * @param ViewListJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        return new self(
            array_map(fn (array $view): string => $view["id"], $array["results"]),
            $array["next_cursor"],
            $array["has_more"],
        );
    }
}
