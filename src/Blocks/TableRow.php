<?php

namespace Notion\Blocks;

use Notion\Common\RichText;
use Notion\Exceptions\BlockException;

/**
 * @psalm-import-type BlockMetadataJson from BlockMetadata
 * @psalm-import-type RichTextJson from \Notion\Common\RichText
 *
 * @psalm-type TableRowJson = array{
 *      table_row: array{
 *          cells: list<list<RichTextJson>>
 *      },
 * }
 *
 * @psalm-immutable
 */
class TableRow implements BlockInterface
{
    /** @param RichText[][] $cells */
    private function __construct(
        private readonly BlockMetadata $metadata,
        public readonly array $cells,
    ) {
        $metadata->checkType(BlockType::TableRow);
    }

    public static function create(): self
    {
        $block = BlockMetadata::create(BlockType::TableRow);

        return new self($block, []);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var BlockMetadataJson $array */
        $metadata = BlockMetadata::fromArray($array);

        /** @psalm-var TableRowJson $array */
        $cells = array_map(
            fn(array $cell) => array_map(fn(array $text) => RichText::fromArray($text), $cell),
            $array["table_row"]["cells"],
        );

        return new self($metadata, $cells);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();

        $array["table_row"] = [
            "cells" => array_map(
                fn(array $c) => array_map(fn(RichText $t) => $t->toArray(), $c),
                $this->cells
            ),
        ];

        return $array;
    }

    public function addCell(RichText ...$cell): self
    {
        $cells = $this->cells;
        $cells[] = $cell;

        return new self($this->metadata, $cells);
    }

    public function metadata(): BlockMetadata
    {
        return $this->metadata;
    }

    public function changeChildren(BlockInterface ...$children): self
    {
        throw BlockException::noChindrenSupport();
    }

    public function addChild(BlockInterface $child): self
    {
        throw BlockException::noChindrenSupport();
    }

    public function archive(): BlockInterface
    {
        return new self(
            $this->metadata->archive(),
            $this->cells,
        );
    }
}
