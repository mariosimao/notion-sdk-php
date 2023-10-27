<?php

namespace Notion\Test\Unit\Blocks;

use Notion\Blocks\BlockType;
use Notion\Blocks\Table;
use Notion\Blocks\TableRow;
use Notion\Common\RichText;
use PHPUnit\Framework\TestCase;

class TableTest extends TestCase
{
    public function test_crate_empty_table(): void
    {
        $table = Table::create();

        $this->assertEmpty($table->rows);
        $this->assertFalse($table->hasColumnHeader);
        $this->assertFalse($table->hasRowHeader);
        $this->assertSame(1, $table->tableWidth);
        $this->assertSame(BlockType::Table, $table->metadata()->type);
    }

    public function test_change_width(): void
    {
        $table = Table::create()->changeWidth(3);

        $this->assertSame(3, $table->tableWidth);
    }

    public function test_enable_column_header(): void
    {
        $table = Table::create()->enableColumnHeader();

        $this->assertTrue($table->hasColumnHeader);
    }

    public function test_disable_column_header(): void
    {
        $table = Table::create()->enableColumnHeader()->disableColumnHeader();

        $this->assertFalse($table->hasColumnHeader);
    }

    public function test_enable_row_header(): void
    {
        $table = Table::create()->enableRowHeader();

        $this->assertTrue($table->hasRowHeader);
    }

    public function test_disable_row_header(): void
    {
        $table = Table::create()->enableRowHeader()->disableRowHeader();

        $this->assertFalse($table->hasRowHeader);
    }

    public function test_change_rows(): void
    {
        $rows = [
            $this->createRow("A1", "B1"),
            $this->createRow("A2", "B2"),
            $this->createRow("A3", "B3"),
        ];

        $table = Table::create()
                      ->changeWidth(2)
                      ->changeRows(...$rows);

        $this->assertEquals($rows, $table->rows);
    }

    public function test_add_row(): void
    {
        $row = $this->createRow("A1", "B1");

        $table = Table::create()
                      ->changeWidth(2)
                      ->addRow($row);

        $this->assertEquals($row, $table->rows[0]);
    }

    public function test_remove_all_rows(): void
    {
        $row = $this->createRow("A1", "B1");

        $table = Table::create()
                      ->changeWidth(2)
                      ->addRow($row)
                      ->changeRows();

        $this->assertEmpty($table->rows);
    }

    private function createRow(string $col1, string $col2): TableRow
    {
        return TableRow::create()
                       ->addCell(RichText::fromString($col1))
                       ->addCell(RichText::fromString($col2));
    }
}
