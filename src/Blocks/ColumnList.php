<?php

namespace Notion\Blocks;

use Notion\NotionException;

/**
 * @psalm-import-type BlockJson from Block
 *
 * @psalm-type ColumnListJson = array{
 *     column_list: array{
 *         children: list<BlockJson>
 *     },
 * }
 *
 * @psalm-immutable
 */
class ColumnList implements BlockInterface
{
    private const TYPE = Block::TYPE_COLUMN_LIST;

    private Block $block;

    /** @var list<Column> */
    private $columns;

    /** @param list<Column> $columns */
    private function __construct(Block $block, array $columns)
    {
        $this->block = $block;
        $this->columns = $columns;
    }

    /** @param list<Column> $columns */
    public static function create(array $columns): self
    {
        $block = Block::create(self::TYPE);

        return new self($block, $columns);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var BlockJson $array */
        $block = Block::fromArray($array);

        /** @psalm-var ColumnListJson $array */
        $rawColumns = $array[self::TYPE]["children"] ?? [];
        /** @var list<Column> $columns */
        $columns = array_map(fn($child) => BlockFactory::fromArray($child), $rawColumns);

        return new self($block, $columns);
    }

    public function toArray(): array
    {
        $array = $this->block->toArray();

        $array[self::TYPE] = [
            "children" => array_map(fn (Column $c) => $c->toArray(), $this->columns),
        ];

        return $array;
    }

    /** @internal */
    public function toUpdateArray(): array
    {
        return [
            self::TYPE => new \stdClass(),
            "archived" => $this->block()->archived(),
        ];
    }

    public function block(): Block
    {
        return $this->block;
    }

    /** @return list<Column> */
    public function columns(): array
    {
        return $this->columns;
    }

    public function changeChildren(array $children): self
    {
        foreach ($children as $child) {
            if ($child::class !== Column::class) {
                throw new NotionException(
                    "Column lists accept only columns as children.",
                    "validation_error",
                );
            }
        }

        /** @var list<Column> $children */
        return new self($this->block(), $children);
    }

    public function archive(): BlockInterface
    {
        return new self(
            $this->block->archive(),
            $this->columns,
        );
    }
}
