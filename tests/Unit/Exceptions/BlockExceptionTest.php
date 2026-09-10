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
}
