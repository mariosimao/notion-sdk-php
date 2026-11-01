<?php

namespace Notion\Test\Unit\Blocks\MeetingNotes\Query;

use DateTimeImmutable;
use Notion\Blocks\MeetingNotes\Query\DateFilter;
use Notion\Blocks\MeetingNotes\Query\DateRange;
use Notion\Blocks\MeetingNotes\Query\DateUnit;
use Notion\Blocks\MeetingNotes\Query\Operator;
use Notion\Blocks\MeetingNotes\Query\Property;
use Notion\Blocks\MeetingNotes\Query\RelativeDateRange;
use Notion\DataSources\Query\RelativeDate;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class DateFilterTest extends TestCase
{
    public function test_properties(): void
    {
        $this->assertSame(Property::CreatedTime, DateFilter::createdTime()->property());
        $this->assertSame(Property::LastEditedTime, DateFilter::lastEditedTime()->property());
        $this->assertSame(Operator::IsNotEmpty, DateFilter::createdTime()->operator());
    }

    #[DataProvider("pointOperators")]
    public function test_point_operators(DateFilter $filter, string $operator): void
    {
        $expected = [
            "property" => "created_time",
            "filter"   => [
                "operator" => $operator,
                "value"    => [
                    "type"  => "exact",
                    "value" => [ "type" => "date", "start_date" => "2026-01-15" ],
                ],
            ],
        ];

        $this->assertSame($expected, $filter->toArray());
    }

    /** @return array<string, array{DateFilter, string}> */
    public static function pointOperators(): array
    {
        $date = new DateTimeImmutable("2026-01-15T10:30:00Z");

        return [
            "equals"     => [ DateFilter::createdTime()->equals($date), "date_is" ],
            "before"     => [ DateFilter::createdTime()->before($date), "date_is_before" ],
            "after"      => [ DateFilter::createdTime()->after($date), "date_is_after" ],
            "onOrBefore" => [ DateFilter::createdTime()->onOrBefore($date), "date_is_on_or_before" ],
            "onOrAfter"  => [ DateFilter::createdTime()->onOrAfter($date), "date_is_on_or_after" ],
        ];
    }

    public function test_relative_date(): void
    {
        $this->assertSame(
            [
                "property" => "last_edited_time",
                "filter"   => [
                    "operator" => "date_is_on_or_after",
                    "value"    => [ "type" => "relative", "value" => "one_week_ago" ],
                ],
            ],
            DateFilter::lastEditedTime()->onOrAfter(RelativeDate::OneWeekAgo)->toArray(),
        );
    }

    public function test_datetime_with_iana_time_zone(): void
    {
        $date = new DateTimeImmutable("2026-01-15 09:30", new \DateTimeZone("America/Los_Angeles"));

        $this->assertSame(
            [
                "property" => "created_time",
                "filter"   => [
                    "operator" => "date_is_after",
                    "value"    => [
                        "type"  => "exact",
                        "value" => [
                            "type"       => "datetime",
                            "start_date" => "2026-01-15",
                            "start_time" => "09:30",
                            "time_zone"  => "America/Los_Angeles",
                        ],
                    ],
                ],
            ],
            DateFilter::createdTime()->after($date, includeTime: true)->toArray(),
        );
    }

    public function test_datetime_with_offset_is_converted_to_utc(): void
    {
        $date = new DateTimeImmutable("2026-01-15T09:30:00+02:00");

        $this->assertSame(
            [
                "property" => "created_time",
                "filter"   => [
                    "operator" => "date_is_before",
                    "value"    => [
                        "type"  => "exact",
                        "value" => [
                            "type"       => "datetime",
                            "start_date" => "2026-01-15",
                            "start_time" => "07:30",
                            "time_zone"  => "UTC",
                        ],
                    ],
                ],
            ],
            DateFilter::createdTime()->before($date, true)->toArray(),
        );
    }

    public function test_is_within(): void
    {
        $this->assertSame(
            [
                "property" => "created_time",
                "filter"   => [
                    "operator" => "date_is_within",
                    "value"    => [ "type" => "relative", "value" => "this_week" ],
                ],
            ],
            DateFilter::createdTime()->isWithin(DateRange::relative(RelativeDateRange::ThisWeek))->toArray(),
        );
    }

    public function test_is_relative_to_with_use_end(): void
    {
        $this->assertSame(
            [
                "property" => "created_time",
                "filter"   => [
                    "operator" => "date_is_relative_to",
                    "value"    => [ "type" => "relative", "value" => "surrounding", "unit" => "month" ],
                    "use_end"  => true,
                ],
            ],
            DateFilter::createdTime()
                ->useEnd()
                ->isRelativeTo(DateRange::surrounding(DateUnit::Month))
                ->toArray(),
        );
    }

    public function test_use_end_can_be_disabled(): void
    {
        $filter = DateFilter::createdTime()
            ->equals(RelativeDate::Today)
            ->useEnd()
            ->useEnd(false);

        $this->assertSame(
            [
                "property" => "created_time",
                "filter"   => [
                    "operator" => "date_is",
                    "value"    => [ "type" => "relative", "value" => "today" ],
                ],
            ],
            $filter->toArray(),
        );
    }

    public function test_empty_operators(): void
    {
        $this->assertSame(
            [ "property" => "created_time", "filter" => [ "operator" => "is_empty" ] ],
            DateFilter::createdTime()->useEnd()->isEmpty()->toArray(),
        );
        $this->assertSame(
            [ "property" => "last_edited_time", "filter" => [ "operator" => "is_not_empty" ] ],
            DateFilter::lastEditedTime()->isNotEmpty()->toArray(),
        );
    }
}
