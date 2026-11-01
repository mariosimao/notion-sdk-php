<?php

namespace Notion\Test\Unit\Blocks\MeetingNotes;

use InvalidArgumentException;
use Notion\Blocks\MeetingNotes\Query;
use Notion\Blocks\MeetingNotes\Query\CompoundFilter;
use Notion\Blocks\MeetingNotes\Query\PeopleFilter;
use Notion\Blocks\MeetingNotes\Query\Sort;
use Notion\Blocks\MeetingNotes\Query\TextFilter;
use PHPUnit\Framework\TestCase;

class QueryTest extends TestCase
{
    public function test_empty_query(): void
    {
        $query = Query::create();

        $this->assertNull($query->filter);
        $this->assertSame([], $query->sorts);
        $this->assertNull($query->limit);
        $this->assertSame([], $query->toArray());
    }

    public function test_property_filter_is_wrapped_in_combinator(): void
    {
        $query = Query::create()->changeFilter(TextFilter::title()->contains("standup"));

        $expected = [
            "filter" => [
                "operator" => "and",
                "filters"  => [
                    [
                        "property" => "title",
                        "filter"   => [
                            "operator" => "string_contains",
                            "value"    => [ "type" => "exact", "value" => "standup" ],
                        ],
                    ],
                ],
            ],
        ];
        $this->assertSame($expected, $query->toArray());
    }

    public function test_compound_filter_sorts_and_limit(): void
    {
        $query = Query::create()
            ->changeFilter(CompoundFilter::or(
                TextFilter::title()->contains("standup"),
                PeopleFilter::attendees()->isNotEmpty(),
            ))
            ->addSort(Sort::lastEditedTime()->descending())
            ->addSort(Sort::title())
            ->changeLimit(20);

        $expected = [
            "filter" => [
                "operator" => "or",
                "filters"  => [
                    [
                        "property" => "title",
                        "filter"   => [
                            "operator" => "string_contains",
                            "value"    => [ "type" => "exact", "value" => "standup" ],
                        ],
                    ],
                    [
                        "property" => "attendees",
                        "filter"   => [ "operator" => "is_not_empty" ],
                    ],
                ],
            ],
            "sort" => [
                [ "property" => "last_edited_time", "direction" => "descending" ],
                [ "property" => "title", "direction" => "ascending" ],
            ],
            "limit" => 20,
        ];
        $this->assertSame($expected, $query->toArray());
    }

    public function test_change_sorts(): void
    {
        $query = Query::create()
            ->addSort(Sort::title())
            ->changeSorts(Sort::createdTime());

        $this->assertSame([[ "property" => "created_time", "direction" => "ascending" ]], $query->toArray()["sort"]);
    }

    public function test_remove_filter_and_limit(): void
    {
        $query = Query::create()
            ->changeFilter(TextFilter::title()->isEmpty())
            ->changeLimit(10)
            ->removeFilter()
            ->removeLimit();

        $this->assertSame([], $query->toArray());
    }

    public function test_limit_too_low(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Query::create()->changeLimit(0);
    }

    public function test_limit_too_high(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Query::create()->changeLimit(Query::MAX_LIMIT + 1);
    }

    public function test_too_many_sorts(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Query::create()->changeSorts(...array_fill(0, Query::MAX_SORTS + 1, Sort::title()));
    }
}
