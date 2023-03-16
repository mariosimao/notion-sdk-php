<?php

namespace Notion\Test\Unit\Search;

use Notion\Search\FilterValue;
use Notion\Search\Query;
use Notion\Search\SortDirection;
use PHPUnit\Framework\TestCase;

class QueryTest extends TestCase
{
    public function test_term(): void
    {
        $q = Query::title("Search term");

        $this->assertSame("Search term", $q->query);
        $this->assertNull($q->filter);
        $this->assertNull($q->sort);
        $this->assertNull($q->startCursor);
        $this->assertNull($q->pageSize);
    }

    public function test_query_all(): void
    {
        $q = Query::all();

        $this->assertNull($q->query);
    }

    public function test_filter_by_pages(): void
    {
        $q = Query::title("Term")->filterByPages();

        $this->assertSame(FilterValue::Page, $q->filter?->value);
    }

    public function test_filter_by_databases(): void
    {
        $q = Query::title("Term")->filterByDatabases();

        $this->assertSame(FilterValue::Database, $q->filter?->value);
    }

    public function test_sort_ascending(): void
    {
        $q = Query::title("Term")->sortByLastEditedTime(SortDirection::Ascending);

        $this->assertSame(SortDirection::Ascending, $q->sort?->direction);
    }

    public function test_sort_descending(): void
    {
        $q = Query::title("Term")->sortByLastEditedTime(SortDirection::Descending);

        $this->assertSame(SortDirection::Descending, $q->sort?->direction);
    }

    public function test_change_start_cursor(): void
    {
        $q = Query::title("Term")->changeStartCursor("abc123");

        $this->assertSame("abc123", $q->startCursor);
    }

    public function test_change_page_size(): void
    {
        $q = Query::title("Term")->changePageSize(3);

        $this->assertSame(3, $q->pageSize);
    }

    public function test_to_array(): void
    {
        $q = Query::title("Term")
            ->filterByPages()
            ->sortByLastEditedTime(SortDirection::Ascending)
            ->changeStartCursor("abc123")
            ->changePageSize(10);

        $expected = [
            "query" => "Term",
            "filter" => [
                "value" => "page",
                "property" => "object",
            ],
            "sort" => [
                "direction" => "ascending",
                "timestamp" => "last_edited_time",
            ],
            "start_cursor" => "abc123",
            "page_size" => 10,
        ];
        $this->assertSame($expected, $q->toArray());
    }
}
