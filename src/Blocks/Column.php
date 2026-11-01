<?php

namespace Notion\Blocks;

use Notion\Exceptions\BlockException\ColumnException;

/**
 * @psalm-import-type BlockMetadataJson from BlockMetadata
 *
 * @psalm-type ColumnJson = array{
 *     column: array {
 *         children?: list<BlockMetadataJson>,
 *         width_ratio?: float|int,
 *     },
 * }
 *
 * @psalm-immutable
 */
final readonly class Column implements BlockInterface
{
    /**
     * @param BlockInterface[] $children
     * @param float|null $widthRatio Width relative to other columns in the same column list, between 0 and 1.
     */
    private function __construct(
        private BlockMetadata $block,
        public array $children,
        public float|null $widthRatio = null,
    ) {
        if ($widthRatio !== null && ($widthRatio <= 0 || $widthRatio > 1)) {
            throw ColumnException::invalidWidthRatio($widthRatio);
        }

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

        $rawWidthRatio = $array["column"]["width_ratio"] ?? null;
        $widthRatio = $rawWidthRatio === null ? null : (float) $rawWidthRatio;

        return new self($block, $children, $widthRatio);
    }

    public function addChild(BlockInterface $child): self
    {
        return new self($this->block, [...$this->children, $child], $this->widthRatio);
    }

    public function changeChildren(BlockInterface ...$children): self
    {
        return new self($this->block, $children, $this->widthRatio);
    }

    /** @param float $widthRatio Number greater than 0 and less than or equal to 1. */
    public function changeWidthRatio(float $widthRatio): self
    {
        return new self($this->block, $this->children, $widthRatio);
    }

    public function removeWidthRatio(): self
    {
        return new self($this->block, $this->children, null);
    }

    public function toArray(): array
    {
        $array = $this->metadata()->toArray();

        $array["column"] = [
            "children" => array_map(fn ($child) => $child->toArray(), $this->children),
        ];

        if ($this->widthRatio !== null) {
            $array["column"]["width_ratio"] = $this->widthRatio;
        }

        return $array;
    }

    public function metadata(): BlockMetadata
    {
        return $this->block;
    }

    public function delete(): BlockInterface
    {
        return new self(
            $this->block->delete(),
            $this->children,
            $this->widthRatio,
        );
    }

    /**
     * @deprecated 1.17.0 Use `delete()` instead.
     * @codeCoverageIgnore
     */
    public function archive(): BlockInterface
    {
        return $this->delete();
    }
}
