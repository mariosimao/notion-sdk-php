<?php

namespace Notion\Test\Unit\Databases\Query;

use Notion\Databases\Query\CompoundFilter;
use Notion\Databases\Query\DateFilter;
use Notion\Databases\Query\SelectFilter;
use Notion\Databases\Query\TextFilter;
use PHPUnit\Framework\TestCase;
use stdClass;

class CompoundFilterTest extends TestCase
{
    public function test_and(): void
    {
        $filter = CompoundFilter::and(
            TextFilter::property("Title")->isNotEmpty(),
            DateFilter::createdTime()->pastWeek()
        );

        $expected = [
            "and" => [
                [
                    "property" => "Title",
                    "rich_text" => [ "is_not_empty" => true ],
                ],
                [
                    "timestamp" => "created_time",
                    "created_time" => [ "past_week" => new stdClass() ],
                ],
            ],
        ];
        $this->assertEquals($expected, $filter->toArray());
    }

    public function test_or(): void
    {
        $filter = CompoundFilter::or(
            TextFilter::property("Title")->isNotEmpty(),
            DateFilter::createdTime()->pastWeek()
        );

        $expected = [
            "or" => [
                [
                    "property" => "Title",
                    "rich_text" => [ "is_not_empty" => true ],
                ],
                [
                    "timestamp" => "created_time",
                    "created_time" => [ "past_week" => new stdClass() ],
                ],
            ],
        ];
        $this->assertEquals($expected, $filter->toArray());
    }

    public function test_nested(): void
    {
        // Drama movies from the 70s or 90s
        $filter = CompoundFilter::or(
            CompoundFilter::and(
                DateFilter::property("Release date")->onOrAfter("1990-01-01"),
                DateFilter::property("Release date")->onOrBefore("1999-12-31"),
            ),
            CompoundFilter::and(
                DateFilter::property("Release date")->onOrAfter("1970-01-01"),
                DateFilter::property("Release date")->onOrBefore("1979-12-31"),
            ),
        );

        $expected = [
            "or" => [
                [
                    "and" => [
                        [
                            "property" => "Release date",
                            "date" => [ "on_or_after" => "1990-01-01" ],
                        ],
                        [
                            "property" => "Release date",
                            "date" => [ "on_or_before" => "1999-12-31" ],
                        ],
                    ],
                ],
                [
                    "and" => [
                        [
                            "property" => "Release date",
                            "date" => [ "on_or_after" => "1970-01-01" ],
                        ],
                        [
                            "property" => "Release date",
                            "date" => [ "on_or_before" => "1979-12-31" ],
                        ],
                    ],
                ],
            ],
        ];
        $this->assertSame($expected, $filter->toArray());
    }
}
