<?php

namespace Notion\Test\Unit\Blocks\MeetingNotes\Query;

use InvalidArgumentException;
use Notion\Blocks\MeetingNotes\Query\CompoundFilter;
use Notion\Blocks\MeetingNotes\Query\PeopleFilter;
use Notion\Blocks\MeetingNotes\Query\TextFilter;
use PHPUnit\Framework\TestCase;

class CompoundFilterTest extends TestCase
{
    public function test_and(): void
    {
        $filter = CompoundFilter::and(
            TextFilter::title()->contains("planning"),
            PeopleFilter::attendees()->isNotEmpty(),
        );

        $expected = [
            "operator" => "and",
            "filters"  => [
                [
                    "property" => "title",
                    "filter"   => [
                        "operator" => "string_contains",
                        "value"    => [ "type" => "exact", "value" => "planning" ],
                    ],
                ],
                [
                    "property" => "attendees",
                    "filter"   => [ "operator" => "is_not_empty" ],
                ],
            ],
        ];

        $this->assertSame($expected, $filter->toArray());
        $this->assertCount(2, $filter->filters());
    }

    public function test_one_level_of_nesting(): void
    {
        $filter = CompoundFilter::and(
            PeopleFilter::attendees()->containsMe(),
            CompoundFilter::or(
                TextFilter::title()->contains("standup"),
                TextFilter::title()->contains("sprint"),
            ),
        );

        $expected = [
            "operator" => "and",
            "filters"  => [
                [
                    "property" => "attendees",
                    "filter"   => [
                        "operator" => "person_contains",
                        "value"    => [[ "type" => "relative", "value" => "me" ]],
                    ],
                ],
                [
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
                            "property" => "title",
                            "filter"   => [
                                "operator" => "string_contains",
                                "value"    => [ "type" => "exact", "value" => "sprint" ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $this->assertSame($expected, $filter->toArray());
    }

    public function test_deep_nesting_is_not_allowed(): void
    {
        $this->expectException(InvalidArgumentException::class);
        CompoundFilter::and(
            CompoundFilter::or(
                CompoundFilter::and(TextFilter::title()->isEmpty()),
            ),
        );
    }

    public function test_too_many_filters(): void
    {
        $this->expectException(InvalidArgumentException::class);
        CompoundFilter::or(...array_fill(0, CompoundFilter::MAX_FILTERS + 1, TextFilter::title()));
    }
}
