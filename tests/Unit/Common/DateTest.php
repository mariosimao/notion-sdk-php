<?php

namespace Notion\Test\Unit\Common;

use DateTimeImmutable;
use DateTimeZone;
use Notion\Common\Date;
use PHPUnit\Framework\TestCase;

class DateTest extends TestCase
{
    public function test_array_conversion(): void
    {
        $array = [
            "start" => "2021-01-01T00:00:00.000000Z",
            "end"   => "2021-12-31T00:00:00.000000Z",
        ];

        $date = Date::fromArray($array);
        $this->assertEquals($array, $date->toArray());
        $this->assertNull($date->timeZone);
        $this->assertFalse($date->hasTimeZone());
    }

    public function test_array_conversion_with_time_zone(): void
    {
        $array = [
            "start"     => "2021-01-01T00:00:00.000000",
            "end"       => "2021-12-31T00:00:00.000000",
            "time_zone" => "America/New_York",
        ];

        $date = Date::fromArray($array);
        $this->assertEquals($array, $date->toArray());
        $this->assertSame("America/New_York", $date->timeZone?->getName());
        $this->assertTrue($date->hasTimeZone());
    }

    public function test_array_conversion_with_null_time_zone(): void
    {
        $array = [
            "start"     => "2021-01-01T00:00:00.000000Z",
            "end"       => "2021-12-31T00:00:00.000000Z",
            "time_zone" => null,
        ];

        $date = Date::fromArray($array);
        $this->assertNull($date->timeZone);
        $this->assertFalse($date->hasTimeZone());
        $this->assertEquals([
            "start" => "2021-01-01T00:00:00.000000Z",
            "end"   => "2021-12-31T00:00:00.000000Z",
        ], $date->toArray());
    }

    public function test_create_date(): void
    {
        $start = new DateTimeImmutable("2021-01-01");
        $date = Date::create($start);

        $this->assertSame($start, $date->start);
        $this->assertNull($date->end);
        $this->assertFalse($date->isRange());
        $this->assertNull($date->timeZone);
        $this->assertFalse($date->hasTimeZone());
    }

    public function test_create_date_with_time_zone_from_name(): void
    {
        $start = new DateTimeImmutable("2021-01-01 12:00:00", new DateTimeZone("America/New_York"));
        $date = Date::create($start, new DateTimeZone("America/New_York"));

        $this->assertSame("America/New_York", $date->timeZone?->getName());
        $this->assertTrue($date->hasTimeZone());
        $this->assertEquals([
            "start"     => "2021-01-01T12:00:00.000000",
            "end"       => null,
            "time_zone" => "America/New_York",
        ], $date->toArray());
    }

    public function test_create_date_with_time_zone_object(): void
    {
        $tz = new DateTimeZone("Europe/London");
        $start = new DateTimeImmutable("2021-01-01 12:00:00", $tz);
        $date = Date::create($start, $tz);

        $this->assertSame($tz, $date->timeZone);
        $this->assertTrue($date->hasTimeZone());
    }

    public function test_create_range(): void
    {
        $start = new DateTimeImmutable("2021-01-01");
        $end = new DateTimeImmutable("2021-12-31");
        $date = Date::createRange($start, $end);

        $this->assertSame($start, $date->start);
        $this->assertSame($end, $date->end);
        $this->assertTrue($date->isRange());
        $this->assertNull($date->timeZone);
        $this->assertFalse($date->hasTimeZone());
    }

    public function test_create_range_with_time_zone(): void
    {
        $start = new DateTimeImmutable("2021-01-01 09:00:00", new DateTimeZone("America/New_York"));
        $end = new DateTimeImmutable("2021-01-01 17:00:00", new DateTimeZone("America/New_York"));
        $date = Date::createRange($start, $end, new DateTimeZone("America/New_York"));

        $this->assertSame("America/New_York", $date->timeZone?->getName());
        $this->assertTrue($date->isRange());
        $this->assertTrue($date->hasTimeZone());
        $this->assertEquals([
            "start"     => "2021-01-01T09:00:00.000000",
            "end"       => "2021-01-01T17:00:00.000000",
            "time_zone" => "America/New_York",
        ], $date->toArray());
    }

    public function test_change_start(): void
    {
        $oldStart = new DateTimeImmutable("2021-01-01");
        $newStart = new DateTimeImmutable("2022-01-01");

        $date = Date::create($oldStart)->changeStart($newStart);

        $this->assertSame($newStart, $date->start);
    }

    public function test_change_start_preserves_time_zone(): void
    {
        $start = new DateTimeImmutable("2021-01-01 12:00:00", new DateTimeZone("America/New_York"));
        $newStart = new DateTimeImmutable("2022-01-01 12:00:00", new DateTimeZone("America/New_York"));

        $date = Date::create($start, new DateTimeZone("America/New_York"))->changeStart($newStart);

        $this->assertSame("America/New_York", $date->timeZone?->getName());
        $this->assertTrue($date->hasTimeZone());
    }

    public function test_change_end(): void
    {
        $start = new DateTimeImmutable("2021-01-01");
        $end = new DateTimeImmutable("2022-01-01");

        $date = Date::create($start)->changeEnd($end);

        $this->assertSame($end, $date->end);
    }

    public function test_change_end_preserves_time_zone(): void
    {
        $start = new DateTimeImmutable("2021-01-01 12:00:00", new DateTimeZone("America/New_York"));
        $end = new DateTimeImmutable("2021-01-02 12:00:00", new DateTimeZone("America/New_York"));

        $date = Date::create($start, new DateTimeZone("America/New_York"))->changeEnd($end);

        $this->assertSame("America/New_York", $date->timeZone?->getName());
        $this->assertTrue($date->hasTimeZone());
    }

    public function test_remove_end(): void
    {
        $start = new DateTimeImmutable("2021-01-01");
        $end = new DateTimeImmutable("2021-12-31");
        $date = Date::createRange($start, $end)->removeEnd();

        $this->assertNull($date->end);
        $this->assertFalse($date->isRange());
    }

    public function test_remove_end_preserves_time_zone(): void
    {
        $start = new DateTimeImmutable("2021-01-01 12:00:00", new DateTimeZone("America/New_York"));
        $end = new DateTimeImmutable("2021-01-02 12:00:00", new DateTimeZone("America/New_York"));

        $date = Date::createRange($start, $end, new DateTimeZone("America/New_York"))->removeEnd();

        $this->assertNull($date->end);
        $this->assertSame("America/New_York", $date->timeZone?->getName());
    }

    public function test_change_time_zone(): void
    {
        $start = new DateTimeImmutable("2021-01-01 12:00:00");
        $date = Date::create($start)->changeTimeZone(new DateTimeZone("America/New_York"));

        $this->assertSame("America/New_York", $date->timeZone?->getName());
        $this->assertTrue($date->hasTimeZone());
    }

    public function test_remove_time_zone(): void
    {
        $start = new DateTimeImmutable("2021-01-01 12:00:00", new DateTimeZone("America/New_York"));
        $date = Date::create($start, new DateTimeZone("America/New_York"))->removeTimeZone();

        $this->assertNull($date->timeZone);
        $this->assertFalse($date->hasTimeZone());
    }

    public function test_now(): void
    {
        $now = new DateTimeImmutable("now");
        $date = Date::now();

        $this->assertNull($date->end);
        $this->assertSame($now->format("Y-m-d"), $date->start->format("Y-m-d"));
        $this->assertNull($date->timeZone);
    }

    public function test_now_with_time_zone(): void
    {
        $date = Date::now(new DateTimeZone("America/New_York"));

        $this->assertNull($date->end);
        $this->assertSame("America/New_York", $date->timeZone?->getName());
        $this->assertTrue($date->hasTimeZone());
    }
}
