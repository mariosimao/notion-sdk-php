<?php

namespace Notion\Test\Unit\DataSources\Query;

use DateTimeImmutable;
use Notion\DataSources\Query\CompoundFilter;
use Notion\DataSources\Query\DateFilter;
use Notion\DataSources\Query\SelectFilter;
use Notion\DataSources\Query\TextFilter;
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
                DateFilter::property("Release date")->onOrAfter(new DateTimeImmutable("1990-01-01T00:00:00.000000Z")),
                DateFilter::property("Release date")->onOrBefore(new DateTimeImmutable("1999-12-31T00:00:00.000000Z")),
            ),
            CompoundFilter::and(
                DateFilter::property("Release date")->onOrAfter(new DateTimeImmutable("1970-01-01T00:00:00.000000Z")),
                DateFilter::property("Release date")->onOrBefore(new DateTimeImmutable("1979-12-31T00:00:00.000000Z")),
            ),
        );

        $expected = [
            "or" => [
                [
                    "and" => [
                        [
                            "property" => "Release date",
                            "date" => [ "on_or_after" => "1990-01-01T00:00:00.000000Z" ],
                        ],
                        [
                            "property" => "Release date",
                            "date" => [ "on_or_before" => "1999-12-31T00:00:00.000000Z" ],
                        ],
                    ],
                ],
                [
                    "and" => [
                        [
                            "property" => "Release date",
                            "date" => [ "on_or_after" => "1970-01-01T00:00:00.000000Z" ],
                        ],
                        [
                            "property" => "Release date",
                            "date" => [ "on_or_before" => "1979-12-31T00:00:00.000000Z" ],
                        ],
                    ],
                ],
            ],
        ];
        $this->assertSame($expected, $filter->toArray());
    }
}
