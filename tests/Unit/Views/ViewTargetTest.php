<?php

namespace Notion\Test\Unit\Views;

use Notion\Exceptions\ViewException;
use Notion\Views\ListQuery;
use Notion\Views\ViewPosition;
use Notion\Views\ViewTarget;
use Notion\Views\WidgetPlacement;
use PHPUnit\Framework\TestCase;

class ViewTargetTest extends TestCase
{
    public function test_database(): void
    {
        $this->assertSame([ "database_id" => "db" ], ViewTarget::database("db")->toArray());
        $this->assertSame(
            [ "database_id" => "db", "position" => [ "type" => "start" ] ],
            ViewTarget::database("db", ViewPosition::start())->toArray(),
        );
        $this->assertSame(
            [ "database_id" => "db", "position" => [ "type" => "end" ] ],
            ViewTarget::database("db", ViewPosition::end())->toArray(),
        );
        $this->assertSame(
            [ "database_id" => "db", "position" => [ "type" => "after_view", "view_id" => "v" ] ],
            ViewTarget::database("db", ViewPosition::afterView("v"))->toArray(),
        );
    }

    public function test_dashboard(): void
    {
        $this->assertSame([ "view_id" => "d" ], ViewTarget::dashboard("d")->toArray());
        $this->assertSame(
            [ "view_id" => "d", "placement" => [ "type" => "new_row" ] ],
            ViewTarget::dashboard("d", WidgetPlacement::newRow())->toArray(),
        );
        $this->assertSame(
            [ "view_id" => "d", "placement" => [ "type" => "existing_row", "row_index" => 2 ] ],
            ViewTarget::dashboard("d", WidgetPlacement::existingRow(2))->toArray(),
        );
    }

    public function test_linked_database(): void
    {
        $this->assertSame(
            [ "create_database" => [ "parent" => [ "type" => "page_id", "page_id" => "p" ] ] ],
            ViewTarget::linkedDatabase("p")->toArray(),
        );
        $this->assertSame(
            [
                "create_database" => [
                    "parent"                 => [ "type" => "page_id", "page_id" => "p" ],
                    "position"               => [ "type" => "after_block", "block_id" => "b" ],
                    "show_data_source_title" => true,
                ],
            ],
            ViewTarget::linkedDatabase("p", "b", true)->toArray(),
        );
    }

    public function test_list_query(): void
    {
        $this->assertSame([ "database_id" => "db" ], ListQuery::database("db")->toArray());
        $this->assertSame(
            [ "data_source_id" => "ds", "start_cursor" => "c", "page_size" => 10 ],
            ListQuery::dataSource("ds")->changeStartCursor("c")->changePageSize(10)->toArray(),
        );
    }

    public function test_list_query_invalid_page_size(): void
    {
        $this->expectException(ViewException::class);
        ListQuery::database("db")->changePageSize(101);
    }
}
