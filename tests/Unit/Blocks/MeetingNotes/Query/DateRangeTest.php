<?php

namespace Notion\Test\Unit\Blocks\MeetingNotes\Query;

use DateTimeImmutable;
use InvalidArgumentException;
use Notion\Blocks\MeetingNotes\Query\DateRange;
use Notion\Blocks\MeetingNotes\Query\DateUnit;
use Notion\Blocks\MeetingNotes\Query\RelativeDateRange;
use PHPUnit\Framework\TestCase;

class DateRangeTest extends TestCase
{
    public function test_relative(): void
    {
        $this->assertSame(
            [ "type" => "relative", "value" => "the_past_week" ],
            DateRange::relative(RelativeDateRange::PastWeek)->toArray(),
        );
    }

    public function test_past(): void
    {
        $this->assertSame(
            [ "type" => "relative", "value" => "custom", "direction" => "past", "unit" => "day", "count" => 3 ],
            DateRange::past(3, DateUnit::Day)->toArray(),
        );
    }

    public function test_next(): void
    {
        $this->assertSame(
            [ "type" => "relative", "value" => "custom", "direction" => "future", "unit" => "week", "count" => 2 ],
            DateRange::next(2, DateUnit::Week)->toArray(),
        );
    }

    public function test_invalid_count(): void
    {
        $this->expectException(InvalidArgumentException::class);
        DateRange::past(0, DateUnit::Month);
    }

    public function test_surrounding(): void
    {
        $this->assertSame(
            [ "type" => "relative", "value" => "surrounding", "unit" => "year" ],
            DateRange::surrounding(DateUnit::Year)->toArray(),
        );
    }

    public function test_between(): void
    {
        $range = DateRange::between(new DateTimeImmutable("2026-01-01"), new DateTimeImmutable("2026-01-31"));

        $this->assertSame(
            [
                "type"  => "exact",
                "value" => [ "type" => "daterange", "start_date" => "2026-01-01", "end_date" => "2026-01-31" ],
            ],
            $range->toArray(),
        );
    }

    public function test_between_open_ended(): void
    {
        $this->assertSame(
            [ "type" => "exact", "value" => [ "type" => "daterange", "start_date" => "2026-01-01" ] ],
            DateRange::between(new DateTimeImmutable("2026-01-01"), null)->toArray(),
        );
        $this->assertSame(
            [ "type" => "exact", "value" => [ "type" => "daterange", "end_date" => "2026-01-31" ] ],
            DateRange::between(null, new DateTimeImmutable("2026-01-31"))->toArray(),
        );
    }

    public function test_between_requires_a_bound(): void
    {
        $this->expectException(InvalidArgumentException::class);
        DateRange::between(null, null);
    }
}
