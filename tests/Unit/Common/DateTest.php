<?php

namespace Notion\Test\Unit\Common;

use DateTimeImmutable;
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
}
