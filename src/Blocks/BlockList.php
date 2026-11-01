<?php

namespace Notion\Blocks;

/**
 * A single page of block children returned by the Notion API.
 *
 * @psalm-type BlockListJson = array{
 *      results: list<array{ type: string, ... }>,
 *      next_cursor: string|null,
 *      has_more: bool,
 *      ...
 * }
 *
 * @psalm-immutable
 */
final readonly class BlockList
{
    /** @param list<BlockInterface> $results */
    private function __construct(
        public array $results,
        public string|null $nextCursor,
        public bool $hasMore,
    ) {
    }

    /** @param list<BlockInterface> $results */
    public static function create(array $results, string|null $nextCursor = null, bool $hasMore = false): self
    {
        return new self($results, $nextCursor, $hasMore);
    }

    /**
     * @psalm-param BlockListJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        $results = array_map(
            fn(array $block): BlockInterface => BlockFactory::fromArray($block),
            $array["results"],
        );

        return new self($results, $array["next_cursor"], $array["has_more"]);
    }
}
