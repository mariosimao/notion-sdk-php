<?php

namespace Notion\Pages;

/**
 * Where a new page is inserted within its parent page.
 *
 * @psalm-type PagePositionJson = array{
 *      type: "after_block"|"page_start"|"page_end",
 *      after_block?: array{ id: string },
 * }
 *
 * @psalm-immutable
 */
final readonly class PagePosition
{
    private function __construct(
        public PagePositionType $type,
        public string|null $blockId = null,
    ) {
    }

    public static function afterBlock(string $blockId): self
    {
        return new self(PagePositionType::AfterBlock, $blockId);
    }

    public static function pageStart(): self
    {
        return new self(PagePositionType::PageStart);
    }

    public static function pageEnd(): self
    {
        return new self(PagePositionType::PageEnd);
    }

    /** @psalm-return PagePositionJson */
    public function toArray(): array
    {
        $array = [
            "type" => $this->type->value,
        ];

        if ($this->isAfterBlock()) {
            $array["after_block"] = ["id" => (string) $this->blockId];
        }

        return $array;
    }

    public function isAfterBlock(): bool
    {
        return $this->type === PagePositionType::AfterBlock;
    }

    public function isPageStart(): bool
    {
        return $this->type === PagePositionType::PageStart;
    }

    public function isPageEnd(): bool
    {
        return $this->type === PagePositionType::PageEnd;
    }
}
