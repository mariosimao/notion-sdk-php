<?php

namespace Notion\Blocks;

use Notion\Common\RichText;

/**
 * @psalm-import-type BlockJson from Block
 *
 * @psalm-type ChildPageJson = array{
 *      child_page: array{ title: string },
 * }
 *
 * @psalm-immutable
 */
class ChildPage implements BlockInterface
{
    private const TYPE = Block::TYPE_CHILD_PAGE;

    private Block $block;

    private string $pageTitle;

    private function __construct(Block $block, string $pageTitle)
    {
        if (!$block->isChildPage()) {
            throw new \Exception("Block must be of type " . self::TYPE);
        }

        $this->block = $block;
        $this->pageTitle = $pageTitle;
    }

    public static function create(): self
    {
        $block = Block::create(self::TYPE);

        return new self($block, "");
    }

    public static function fromString(string $pageTitle): self
    {
        $block = Block::create(self::TYPE);

        return new self($block, $pageTitle);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var BlockJson $array */
        $block = Block::fromArray($array);

        /** @psalm-var ChildPageJson $array */
        $pageTitle = $array[self::TYPE]["title"];

        return new self($block, $pageTitle);
    }

    public function toArray(): array
    {
        $array = $this->block->toArray();

        $array[self::TYPE] = [ "title" => $this->pageTitle ];

        return $array;
    }

    public function block(): Block
    {
        return $this->block;
    }

    public function pageTitle(): string
    {
        return $this->pageTitle;
    }

    public function withPageTitle(string $pageTitle): self
    {
        return new self($this->block, $pageTitle);
    }
}
