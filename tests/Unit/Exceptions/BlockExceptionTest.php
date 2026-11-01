<?php

namespace Notion\Test\Unit\Exceptions;

use Notion\Blocks\BlockType;
use Notion\Exceptions\BlockException;
use Notion\Exceptions\BlockException\ColumnException;
use Notion\Exceptions\BlockException\ColumnListException;
use Notion\Exceptions\BlockException\HeadingException;
use Notion\Exceptions\NotionException;
use PHPUnit\Framework\TestCase;

class BlockExceptionTest extends TestCase
{
    public function test_wrong_type(): void
    {
        $e = BlockException::wrongType(BlockType::TableRow);

        $this->assertInstanceOf(NotionException::class, $e);
        $this->assertSame("Block must be of type 'table_row'", $e->getMessage());
    }

    public function test_no_children_support(): void
    {
        $e = BlockException::noChindrenSupport();

        $this->assertInstanceOf(NotionException::class, $e);
        $this->assertSame("This block does not support children.", $e->getMessage());
    }

    public function test_column_inside_column(): void
    {
        $e = ColumnException::columnInsideColumn();

        $this->assertInstanceOf(BlockException::class, $e);
        $this->assertSame("Columns should not contain other columns.", $e->getMessage());
    }

    public function test_child_not_column(): void
    {
        $e = ColumnListException::childNotColumn();

        $this->assertInstanceOf(BlockException::class, $e);
        $this->assertSame("Column lists accept only columns as children.", $e->getMessage());
    }

    public function test_untogglify_with_children(): void
    {
        $e = HeadingException::untogglifyWithChildren();

        $this->assertInstanceOf(BlockException::class, $e);
        $expected = "Heading cannot be un-togglified with children. Please remove child blocks";
        $this->assertSame($expected, $e->getMessage());
    }

    public function test_invalid_column_width_ratio(): void
    {
        $e = ColumnException::invalidWidthRatio(1.5);

        $this->assertSame(
            "Column width ratio must be greater than 0 and less than or equal to 1, 1.5 given.",
            $e->getMessage(),
        );
    }

    public function test_column_width_ratios_exceed_one(): void
    {
        $e = ColumnListException::widthRatiosExceedOne(1.25);

        $this->assertSame("Column width ratios must not add up to more than 1, 1.25 given.", $e->getMessage());
    }

    public function test_column_width_ratios_not_adding_up_to_one(): void
    {
        $e = ColumnListException::widthRatiosNotAddingUpToOne(0.5);

        $this->assertSame(
            "When all columns of a column list have width ratios, they must add up to 1, 0.5 given.",
            $e->getMessage(),
        );
    }
}
