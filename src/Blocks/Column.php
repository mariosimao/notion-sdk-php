<?php

namespace Notion\Blocks;

use Notion\NotionException;

/**
 * @psalm-import-type BlockJson from Block
 *
 * @psalm-type ColumnJson = array{
 *     column: array {
 *         children: list<BlockJson>
 *     },
 * }
 *
 * @psalm-immutable
 */
class Column implements BlockInterface
{
    private const TYPE = Block::TYPE_COLUMN;

    private Block $block;

    /** @var list<BlockInterface> */
    private array $children;

    /** @param list<BlockInterface> $children */
    private function __construct(Block $block, array $children)
    {
        foreach ($children as $child) {
            if ($child::class === Column::class) {
                throw new NotionException(
                    "Columns should not contain other columns.",
                    "validation_error"
                );
            }
        }

        $this->block = $block;
        $this->children = $children;
    }

    /** @param list<BlockInterface> $children */
    public static function create(array $children): self
    {
        $block = Block::create(self::TYPE);

        return new self($block, $children);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var BlockJson $array */
        $block = Block::fromArray($array);

        /** @psalm-var ColumnJson $array */
        $rawChildren = $array[self::TYPE]["children"] ?? [];
        $children = array_map(fn($child) => BlockFactory::fromArray($child), $rawChildren);

        return new self($block, $children);
    }

    /** @param list<BlockInterface> $children */
    public function changeChildren(array $children): self
    {
        return new self($this->block, $children);
    }

    public function appendChild(BlockInterface $child): self
    {
        $children = $this->children;
        array_push($children, $child);

        return new self($this->block, $children);
    }

    public function toArray(): array
    {
        $array = $this->block()->toArray();

        $array[self::TYPE] = [
            "children" => array_map(fn ($child) => $child->toArray(), $this->children),
        ];

        return $array;
    }

    public function block(): Block
    {
        return $this->block;
    }

    /** @return list<BlockInterface> */
    public function children(): array
    {
        return $this->children;
    }
}
