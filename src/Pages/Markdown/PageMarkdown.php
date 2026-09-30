<?php

namespace Notion\Pages\Markdown;

/**
 * @psalm-type PageMarkdownJson = array{
 *      object: "page_markdown",
 *      id: string,
 *      markdown: string,
 *      truncated: bool,
 *      unknown_block_ids: list<string>,
 * }
 *
 * @psalm-immutable
 */
final readonly class PageMarkdown
{
    /** @param list<string> $unknownBlockIds */
    private function __construct(
        public string $id,
        public string $markdown,
        public bool $truncated,
        public array $unknownBlockIds,
    ) {
    }

    /**
     * @psalm-param PageMarkdownJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        return new self(
            $array["id"],
            $array["markdown"],
            $array["truncated"],
            $array["unknown_block_ids"],
        );
    }
}
