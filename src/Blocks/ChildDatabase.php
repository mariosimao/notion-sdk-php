<?php

namespace Notion\Blocks;

use Notion\Common\RichText;

/**
 * @psalm-import-type BlockJson from Block
 *
 * @psalm-type ChildDatabaseJson = array{
 *      child_database: array{ title: string },
 * }
 *
 * @psalm-immutable
 */
class ChildDatabase implements BlockInterface
{
    private const TYPE = Block::TYPE_CHILD_DATABASE;

    private Block $block;

    private string $databaseTitle;

    private function __construct(Block $block, string $databaseTitle)
    {
        if (!$block->isChildDatabase()) {
            throw new \Exception("Block must be of type " . self::TYPE);
        }

        $this->block = $block;
        $this->databaseTitle = $databaseTitle;
    }

    public static function create(): self
    {
        $block = Block::create(self::TYPE);

        return new self($block, "");
    }

    public static function fromString(string $databaseTitle): self
    {
        $block = Block::create(self::TYPE);

        return new self($block, $databaseTitle);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var BlockJson $array */
        $block = Block::fromArray($array);

        /** @psalm-var ChildDatabaseJson $array */
        $databaseTitle = $array[self::TYPE]["title"];

        return new self($block, $databaseTitle);
    }

    public function toArray(): array
    {
        $array = $this->block->toArray();

        $array[self::TYPE] = [ "title" => $this->databaseTitle ];

        return $array;
    }

    public function block(): Block
    {
        return $this->block;
    }

    public function databaseTitle(): string
    {
        return $this->databaseTitle;
    }

    public function withDatabaseTitle(string $databaseTitle): self
    {
        return new self($this->block, $databaseTitle);
    }
}
