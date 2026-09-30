<?php

namespace Notion\Test\Unit\Common;

use DateTimeImmutable;
use DateTimeZone;
use Notion\Common\Date;
use Notion\Exceptions\DateException;
use PHPUnit\Framework\Attributes\DataProvider;
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
    }

    public function test_create_date(): void
    {
        $start = new DateTimeImmutable("2021-01-01");
        $date = Date::create($start);

        $this->assertSame($start, $date->start);
        $this->assertNull($date->end);
        $this->assertFalse($date->isRange());
    }

    public function test_create_range(): void
    {
        $start = new DateTimeImmutable("2021-01-01");
        $end = new DateTimeImmutable("2021-12-31");
        $date = Date::createRange($start, $end);

        $this->assertSame($start, $date->start);
        $this->assertSame($end, $date->end);
        $this->assertTrue($date->isRange());
    }

    public function test_change_start(): void
    {
        $oldStart = new DateTimeImmutable("2021-01-01");
        $newStart = new DateTimeImmutable("2022-01-01");

        $date = Date::create($oldStart)->changeStart($newStart);

        $this->assertSame($newStart, $date->start);
    }

    public function test_change_end(): void
    {
        $start = new DateTimeImmutable("2021-01-01");
        $end = new DateTimeImmutable("2022-01-01");

        $date = Date::create($start)->changeEnd($end);

        $this->assertSame($end, $date->end);
    }

    public function test_remove_end(): void
    {
        $start = new DateTimeImmutable("2021-01-01");
        $end = new DateTimeImmutable("2021-12-31");
        $date = Date::createRange($start, $end)->removeEnd();

        $this->assertNull($date->end);
        $this->assertFalse($date->isRange());
    }

    public function test_now(): void
    {
        $now = new DateTimeImmutable("now");
        $date = Date::now();

        $this->assertNull($date->end);
        $this->assertSame($now->format("Y-m-d"), $date->start->format("Y-m-d"));
    }

    public function test_now_with_time_zone(): void
    {
        $date = Date::now(new DateTimeZone("Asia/Tokyo"));

        $this->assertSame("Asia/Tokyo", $date->timeZone?->getName());
        $this->assertSame("Asia/Tokyo", $date->start->getTimezone()->getName());
    }

    public function test_without_time_zone_by_default(): void
    {
        $date = Date::create(new DateTimeImmutable("2021-01-01T10:00:00+02:00"));

        $this->assertNull($date->timeZone);
        $this->assertFalse($date->hasTimeZone());
        $this->assertArrayNotHasKey("time_zone", $date->toArray());
    }

    public function test_create_with_time_zone_keeps_instant(): void
    {
        $timeZone = new DateTimeZone("America/New_York");
        $date = Date::create(new DateTimeImmutable("2025-06-15T18:30:00Z"), $timeZone);

        $this->assertTrue($date->hasTimeZone());
        $this->assertSame($timeZone, $date->timeZone);
        $this->assertSame("America/New_York", $date->start->getTimezone()->getName());
        $this->assertEquals(new DateTimeImmutable("2025-06-15T18:30:00Z"), $date->start);
        $this->assertSame([
            "start" => "2025-06-15T14:30:00.000000",
            "end" => null,
            "time_zone" => "America/New_York",
        ], $date->toArray());
    }

    public function test_create_range_with_time_zone(): void
    {
        $date = Date::createRange(
            new DateTimeImmutable("2025-01-15T09:00:00Z"),
            new DateTimeImmutable("2025-01-15T17:00:00+01:00"),
            new DateTimeZone("Europe/Lisbon"),
        );

        $this->assertSame([
            "start" => "2025-01-15T09:00:00.000000",
            "end" => "2025-01-15T16:00:00.000000",
            "time_zone" => "Europe/Lisbon",
        ], $date->toArray());
    }

    /** @param non-empty-string $timeZone */
    #[DataProvider("invalidTimeZones")]
    public function test_create_rejects_non_iana_time_zone(string $timeZone): void
    {
        $this->expectException(DateException::class);

        Date::create(new DateTimeImmutable("2025-01-01T00:00:00Z"), new DateTimeZone($timeZone));
    }

    public function test_create_range_rejects_non_iana_time_zone(): void
    {
        $this->expectException(DateException::class);

        Date::createRange(
            new DateTimeImmutable("2025-01-01T00:00:00Z"),
            new DateTimeImmutable("2025-01-02T00:00:00Z"),
            new DateTimeZone("+02:00"),
        );
    }

    public function test_change_time_zone_rejects_non_iana_time_zone(): void
    {
        $this->expectException(DateException::class);

        Date::now()->changeTimeZone(new DateTimeZone("-03:00"));
    }

    /** @return array<string, array{non-empty-string}> */
    public static function invalidTimeZones(): array
    {
        return [
            "offset" => ["+02:00"],
            "abbreviation" => ["CEST"],
        ];
    }

    public function test_from_array_with_time_zone(): void
    {
        $array = [
            "start" => "2025-06-15T14:30:00.000000",
            "end" => "2025-06-15T16:00:00.000000",
            "time_zone" => "America/New_York",
        ];

        $date = Date::fromArray($array);

        $this->assertSame("America/New_York", $date->timeZone?->getName());
        $this->assertEquals(new DateTimeImmutable("2025-06-15T18:30:00Z"), $date->start);
        $this->assertEquals(new DateTimeImmutable("2025-06-15T20:00:00Z"), $date->end);
        $this->assertSame($array, $date->toArray());
    }

    public function test_from_array_with_time_zone_and_offset(): void
    {
        $date = Date::fromArray([
            "start" => "2025-06-15T18:30:00.000Z",
            "end" => null,
            "time_zone" => "America/New_York",
        ]);

        $this->assertEquals(new DateTimeImmutable("2025-06-15T18:30:00Z"), $date->start);
        $this->assertSame("2025-06-15T14:30:00.000000", $date->toArray()["start"]);
    }

    public function test_from_array_with_null_time_zone(): void
    {
        $date = Date::fromArray([
            "start" => "2025-06-15T14:30:00.000-04:00",
            "end" => null,
            "time_zone" => null,
        ]);

        $this->assertNull($date->timeZone);
        $this->assertSame([
            "start" => "2025-06-15T14:30:00.000000-04:00",
            "end" => null,
        ], $date->toArray());
    }

    public function test_change_time_zone_keeps_instants(): void
    {
        $date = Date::createRange(
            new DateTimeImmutable("2025-06-15T12:00:00Z"),
            new DateTimeImmutable("2025-06-15T13:00:00Z"),
        )->changeTimeZone(new DateTimeZone("Asia/Tokyo"));

        $this->assertSame([
            "start" => "2025-06-15T21:00:00.000000",
            "end" => "2025-06-15T22:00:00.000000",
            "time_zone" => "Asia/Tokyo",
        ], $date->toArray());
    }

    public function test_change_start_and_end_with_time_zone(): void
    {
        $date = Date::create(new DateTimeImmutable("2025-06-15T12:00:00Z"), new DateTimeZone("Asia/Tokyo"))
            ->changeStart(new DateTimeImmutable("2025-06-16T00:00:00Z"))
            ->changeEnd(new DateTimeImmutable("2025-06-16T01:00:00Z"));

        $this->assertSame([
            "start" => "2025-06-16T09:00:00.000000",
            "end" => "2025-06-16T10:00:00.000000",
            "time_zone" => "Asia/Tokyo",
        ], $date->toArray());
    }

    public function test_remove_end_keeps_time_zone(): void
    {
        $date = Date::createRange(
            new DateTimeImmutable("2025-06-15T12:00:00Z"),
            new DateTimeImmutable("2025-06-15T13:00:00Z"),
            new DateTimeZone("Asia/Tokyo"),
        )->removeEnd();

        $this->assertSame("Asia/Tokyo", $date->timeZone?->getName());
    }

    public function test_remove_time_zone_keeps_instants(): void
    {
        $date = Date::create(new DateTimeImmutable("2025-06-15T12:00:00Z"), new DateTimeZone("Asia/Tokyo"))
            ->removeTimeZone();

        $this->assertFalse($date->hasTimeZone());
        $this->assertSame([
            "start" => "2025-06-15T21:00:00.000000+09:00",
            "end" => null,
        ], $date->toArray());
    }
}
