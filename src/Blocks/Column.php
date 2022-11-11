<?php

namespace Notion\Blocks;

use Notion\Exceptions\ColumnException;

/**
 * @psalm-import-type BlockMetadataJson from BlockMetadata
 *
 * @psalm-type ColumnJson = array{
 *     column: array {
 *         children: list<BlockMetadataJson>
 *     },
 * }
 *
 * @psalm-immutable
 */
class Column implements BlockInterface
{
    /** @param BlockInterface[] $children */
    private function __construct(
        private readonly BlockMetadata $block,
        public readonly array $children,
    ) {
        foreach ($children as $child) {
            if ($child->metadata()->type === BlockType::Column) {
                throw ColumnException::columnInsideColumn();
            }
        }
    }

    public static function create(BlockInterface ...$children): self
    {
        $block = BlockMetadata::create(BlockType::Column);

        return new self($block, $children);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var BlockMetadataJson $array */
        $block = BlockMetadata::fromArray($array);

        /** @psalm-var ColumnJson $array */
        $rawChildren = $array["column"]["children"] ?? [];
        $children = array_map(fn($child) => BlockFactory::fromArray($child), $rawChildren);

        return new self($block, $children);
    }

    public function addChild(BlockInterface $child): self
    {
        return new self($this->block, [...$this->children, $child]);
    }

    public function changeChildren(BlockInterface ...$children): self
    {
        return new self($this->block, $children);
    }

    public function toArray(): array
    {
        $array = $this->metadata()->toArray();

        $array["column"] = [
            "children" => array_map(fn ($child) => $child->toArray(), $this->children),
        ];

        return $array;
    }

    public function metadata(): BlockMetadata
    {
        return $this->block;
    }

    public function archive(): BlockInterface
    {
        return new self(
            $this->block->archive(),
            $this->children,
        );
    }
}
