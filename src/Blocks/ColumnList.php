<?php

namespace Notion\Blocks;

use Notion\Exceptions\BlockException\ColumnListException;

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
final readonly class ColumnList implements BlockInterface
{
    private const WIDTH_RATIO_TOLERANCE = 0.000001;

    /** @param Column[] $columns */
    private function __construct(
        private BlockMetadata $metadata,
        public array $columns,
    ) {
        $metadata->checkType(BlockType::ColumnList);
    }

    public static function create(Column ...$columns): self
    {
        $metadata = BlockMetadata::create(BlockType::ColumnList);
        self::validateWidthRatios($columns, true);

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
        if (!$child instanceof Column) {
            throw ColumnListException::childNotColumn();
        }

        $columns = [...$this->columns, $child];
        self::validateWidthRatios($columns, false);

        return new self($this->metadata(), $columns);
    }

    public function changeChildren(BlockInterface ...$children): self
    {
        $columns = [];
        foreach ($children as $child) {
            if (!$child instanceof Column) {
                throw ColumnListException::childNotColumn();
            }
            $columns[] = $child;
        }

        self::validateWidthRatios($columns, true);

        return new self($this->metadata(), $columns);
    }

    /**
     * Width ratios provided by columns must not add up to more than 1. When
     * `$complete` is true and every column has a width ratio, they must add
     * up to exactly 1.
     *
     * @param Column[] $columns
     * @psalm-pure
     */
    private static function validateWidthRatios(array $columns, bool $complete): void
    {
        $sum = 0.0;
        $allHaveRatios = $columns !== [];
        foreach ($columns as $column) {
            if ($column->widthRatio === null) {
                $allHaveRatios = false;
                continue;
            }
            $sum += $column->widthRatio;
        }

        if ($sum > 1 + self::WIDTH_RATIO_TOLERANCE) {
            throw ColumnListException::widthRatiosExceedOne($sum);
        }

        if ($complete && $allHaveRatios && abs($sum - 1) > self::WIDTH_RATIO_TOLERANCE) {
            throw ColumnListException::widthRatiosNotAddingUpToOne($sum);
        }
    }

    public function delete(): BlockInterface
    {
        return new self(
            $this->metadata->delete(),
            $this->columns,
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
