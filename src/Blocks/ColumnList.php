<?php

namespace Notion\Blocks;

use Notion\Exceptions\ColumnListException;

/**
 * @psalm-import-type BlockMetadataJson from BlockMetadata
 *
 * @psalm-type ColumnListJson = array{
 *     column_list: array{
 *         children: list<BlockMetadataJson>
 *     },
 * }
 *
 * @psalm-immutable
 */
class ColumnList implements BlockInterface
{
    /** @param Column[] $columns */
    private function __construct(
        private readonly BlockMetadata $metadata,
        public readonly array $columns,
    ) {
        $metadata->checkType(BlockType::ColumnList);
    }

    public static function create(Column ...$columns): self
    {
        $metadata = BlockMetadata::create(BlockType::ColumnList);

        return new self($metadata, $columns);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var BlockMetadataJson $array */
        $metadata = BlockMetadata::fromArray($array);

        /** @psalm-var ColumnListJson $array */
        $rawColumns = $array["column_list"]["children"] ?? [];
        /** @var Column[] $columns */
        $columns = array_map(fn($child) => BlockFactory::fromArray($child), $rawColumns);

        return new self($metadata, $columns);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();

        $array["column_list"] = [
            "children" => array_map(fn (Column $c) => $c->toArray(), $this->columns),
        ];

        return $array;
    }

    public function metadata(): BlockMetadata
    {
        return $this->metadata;
    }

    public function addChild(BlockInterface $child): self
    {
        if ($child->metadata()->type !== BlockType::Column) {
            throw ColumnListException::childNotColumn();
        }

        /** @var Column $child */
        return new self($this->metadata(), [...$this->columns, $child]);
    }

    public function changeChildren(BlockInterface ...$children): self
    {
        foreach ($children as $child) {
            if ($child->metadata()->type !== BlockType::Column) {
                throw ColumnListException::childNotColumn();
            }
        }

        /** @var Column[] $children */
        return new self($this->metadata(), $children);
    }

    public function archive(): BlockInterface
    {
        return new self(
            $this->metadata->archive(),
            $this->columns,
        );
    }
}
