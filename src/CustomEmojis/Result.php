<?php

namespace Notion\CustomEmojis;

/**
 * @psalm-import-type CustomEmojiJson from CustomEmoji
 *
 * @psalm-type ResultJson = array{
 *      results: CustomEmojiJson[],
 *      next_cursor: string|null,
 *      has_more: bool,
 * }
 *
 * @psalm-immutable
 */
final readonly class Result
{
    /** @param CustomEmoji[] $results */
    private function __construct(
        public array $results,
        public string|null $nextCursor,
        public bool $hasMore,
    ) {
    }

    /**
     * @psalm-param ResultJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        $results = array_map(
            fn (array $emoji): CustomEmoji => CustomEmoji::fromArray($emoji),
            $array["results"],
        );

        return new self($results, $array["next_cursor"], $array["has_more"]);
    }
}
