<?php

namespace Notion\Blocks;

use Notion\Exceptions\BlockException;

/**
 * @psalm-import-type BlockMetadataJson from BlockMetadata
 * @psalm-import-type RichTextJson from \Notion\Common\RichText
 *
 * @psalm-type TableJson = array{
 *      table: array{
 *          table_width: int,
 *          has_column_header: bool,
 *          has_row_header: bool,
 *          children: list<BlockMetadataJson>,
 *      },
 * }
 *
 * @psalm-immutable
 */
class Table implements BlockInterface
{
    /** @param TableRow[] $rows */
    private function __construct(
        private readonly BlockMetadata $metadata,
        public readonly int $tableWidth,
        public readonly bool $hasColumnHeader,
        public readonly bool $hasRowHeader,
        public readonly array $rows,
    ) {
        $metadata->checkType(BlockType::Table);
    }

    public static function create(): self
    {
        $block = BlockMetadata::create(BlockType::Table);

        return new self($block, 1, false, false, []);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var BlockMetadataJson $array */
        $block = BlockMetadata::fromArray($array);

        /** @psalm-var TableJson $array */
        $table = $array["table"];

        $tableWidth = $table["table_width"];
        $hasColumnHeader = $table["has_column_header"];
        $hasRowHeader = $table["has_row_header"];
        $rows = array_map(fn(array $row) => TableRow::fromArray($row), $table["children"]);

        return new self($block, $tableWidth, $hasColumnHeader, $hasRowHeader, $rows);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();

        $array["table"] = [
            "table_width"       => $this->tableWidth,
            "has_column_header" => $this->hasColumnHeader,
            "has_row_header"    => $this->hasRowHeader,
            "children"          => array_map(fn(TableRow $row) => $row->toArray(), $this->rows),
        ];

        return $array;
    }

    public function metadata(): BlockMetadata
    {
        return $this->metadata;
    }

    public function changeWidth(int $tableWidth): self
    {
        return new self(
            $this->metadata->update(),
            $tableWidth,
            $this->hasColumnHeader,
            $this->hasRowHeader,
            $this->rows,
        );
    }

    public function enableColumnHeader(): self
    {
        return new self(
            $this->metadata->update(),
            $this->tableWidth,
            true,
            $this->hasRowHeader,
            $this->rows,
        );
    }

    public function disableColumnHeader(): self
    {
        return new self(
            $this->metadata->update(),
            $this->tableWidth,
            false,
            $this->hasRowHeader,
            $this->rows,
        );
    }

    public function enableRowHeader(): self
    {
        return new self(
            $this->metadata->update(),
            $this->tableWidth,
            $this->hasColumnHeader,
            true,
            $this->rows,
        );
    }

    public function disableRowHeader(): self
    {
        return new self(
            $this->metadata->update(),
            $this->tableWidth,
            $this->hasColumnHeader,
            false,
            $this->rows,
        );
    }

    public function changeRows(TableRow ...$rows): self
    {
        $hasChildren = (count($rows) > 0);

        return new self(
            $this->metadata->updateHasChildren($hasChildren),
            $this->tableWidth,
            $this->hasColumnHeader,
            $this->hasRowHeader,
            $rows,
        );
    }

    public function addRow(TableRow $row): self
    {
        $rows = $this->rows;
        $rows[] = $row;

        return new self(
            $this->metadata->updateHasChildren(true),
            $this->tableWidth,
            $this->hasColumnHeader,
            $this->hasRowHeader,
            $rows,
        );
    }

    public function changeChildren(BlockInterface ...$children): self
    {
        foreach ($children as $child) {
            if ($child::class !== TableRow::class) {
                throw BlockException::wrongType(BlockType::TableRow);
            }
        }

        /** @psalm-var TableRow[] $children */
        return $this->changeRows(...$children);
    }

    public function addChild(BlockInterface $child): self
    {
        if ($child::class !== TableRow::class) {
            throw BlockException::wrongType(BlockType::TableRow);
        }

        /** @psalm-var TableRow $child */
        return $this->addRow($child);
    }

    public function archive(): BlockInterface
    {
        return new self(
            $this->metadata->archive(),
            $this->tableWidth,
            $this->hasColumnHeader,
            $this->hasRowHeader,
            $this->rows,
        );
    }
}
