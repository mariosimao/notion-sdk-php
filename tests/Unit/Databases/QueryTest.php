<?php

namespace Notion\Test\Unit\Databases;

use Exception;
use Notion\Databases\Query;
use Notion\Databases\Query\Sort;
use Notion\Databases\Query\TextFilter;
use PHPUnit\Framework\TestCase;

class QueryTest extends TestCase
{
    public function test_empty_query(): void
    {
        $query = Query::create();

        $this->assertNull($query->filter());
        $this->assertEmpty($query->sorts());
        $this->assertNull($query->startCursor());
        $this->assertSame(Query::MAX_PAGE_SIZE, $query->pageSize());
    }

    public function test_query_with_filter(): void
    {
        $query = Query::create()
            ->withFilter(TextFilter::property("Title")->isNotEmpty());

        $this->assertNotNull($query->filter());
    }

    public function test_add_sort(): void
    {
        $query = Query::create()
            ->withAddedSort(Sort::createdTime()->descending())
            ->withAddedSort(Sort::property("Title")->ascending());

        $this->assertCount(2, $query->sorts());
    }

    public function test_replace_sorts(): void
    {
        $query = Query::create()
            ->withAddedSort(Sort::createdTime()->descending())
            ->withAddedSort(Sort::property("Title")->ascending())
            ->withSorts([ Sort::lastEditedTime()->descending() ]);

        $this->assertCount(1, $query->sorts());
    }

    public function test_query_with_start_cursor(): void
    {
        $query = Query::create()
            ->withStartCursor("889431ed-4f50-460b-a926-36f6cf0f9669");

        $this->assertSame("889431ed-4f50-460b-a926-36f6cf0f9669", $query->startCursor());
    }

    public function test_query_with_custom_page_size(): void
    {
        $query = Query::create()
            ->withPageSize(20);

        $this->assertSame(20, $query->pageSize());
    }

    public function test_page_size_more_than_limit(): void
    {
        $this->expectException(Exception::class);

        /** @psalm-suppress UnusedMethodCall */
        Query::create()->withPageSize(100000000);
    }

    public function test_empty_query_to_array(): void
    {
        $query = Query::create();

        $expected = [
            "sorts" => [],
            "page_size" => Query::MAX_PAGE_SIZE,
        ];

        $this->assertSame($expected, $query->toArray());
    }

    public function test_complete_query_to_array(): void
    {
        $query = Query::create()
            ->withFilter(TextFilter::property("Title")->contains("abc"))
            ->withAddedSort(Sort::property("Title")->ascending())
            ->withStartCursor("889431ed-4f50-460b-a926-36f6cf0f9669")
            ->withPageSize(20);

        $expected = [
            "filter" => [
                "property" => "Title",
                "rich_text" => [ "contains" => "abc" ],
            ],
            "sorts" => [
                [
                    "property" => "Title",
                    "direction" => "ascending",
                ],
            ],
            "start_cursor" => "889431ed-4f50-460b-a926-36f6cf0f9669",
            "page_size" => 20,
        ];

        $this->assertEquals($expected, $query->toArray());
    }
}
