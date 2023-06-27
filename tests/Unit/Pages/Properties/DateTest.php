<?php

namespace Notion\Test\Unit\Pages\Properties;

use DateTimeImmutable;
use Notion\Pages\Properties\Date;
use Notion\Pages\Properties\PropertyFactory;
use Notion\Pages\Properties\PropertyType;
use PHPUnit\Framework\TestCase;

class DateTest extends TestCase
{
    public function test_create_date(): void
    {
        $someday = new DateTimeImmutable("2021-01-01");

        $date = Date::create($someday);

        $this->assertEquals($someday, $date->start());
        $this->assertNull($date->end());
        $this->assertFalse($date->isRange());
        $this->assertEquals(PropertyType::Date, $date->metadata()->type);
    }

    public function test_create_range(): void
    {
        $start = new DateTimeImmutable("2021-01-01");
        $end = new DateTimeImmutable("2021-12-31");

        $date = Date::createRange($start, $end);

        $this->assertTrue($date->isRange());
        $this->assertEquals($start, $date->start());
        $this->assertEquals($end, $date->end());
    }

    public function test_create_empty(): void
    {
        $date = Date::createEmpty();

        $this->assertTrue($date->isEmpty());
    }

    public function test_change_start(): void
    {
        $newStart = new DateTimeImmutable("2021-01-01");

        $date = Date::create(new DateTimeImmutable("2020-01-01"))
                    ->changeStart($newStart);

        $this->assertEquals($newStart, $date->start());
    }

    public function test_change_end(): void
    {
        $newEnd = new DateTimeImmutable("2021-12-31");

        $date = Date::create(new DateTimeImmutable("2021-01-01"))
                    ->changeEnd($newEnd);

        $this->assertEquals($newEnd, $date->end());
    }

    public function test_remove_end(): void
    {
        $date = Date::createRange(
            new DateTimeImmutable("2021-01-01"),
            new DateTimeImmutable("2021-12-31"),
        )->removeEnd();

        $this->assertNull($date->end());
        $this->assertFalse($date->isRange());
    }

    public function test_clear(): void
    {
        $someday = new DateTimeImmutable("2021-01-01");

        $date = Date::create($someday)->clear();

        $this->assertTrue($date->isEmpty());
    }

    public function test_array_conversion(): void
    {
        $array = [
            "id"   => "a7ede3b7-c7ae-4eb8-b415-a7f80ac4dfe5",
            "type" => "date",
            "date" => [
                "start" => "2021-01-01T00:00:00.000000Z",
                "end"   => "2021-12-31T00:00:00.000000Z",
            ],
        ];
        $date = Date::fromArray($array);
        $fromFactory = PropertyFactory::fromArray($array);

        $this->assertEquals($array, $date->toArray());
        $this->assertEquals($array, $fromFactory->toArray());
    }

    public function test_is_empty(): void
    {
        $array = [
            "id"   => "a7ede3b7-c7ae-4eb8-b415-a7f80ac4dfe5",
            "type" => "date",
            "date" => null,
        ];
        $date = Date::fromArray($array);

        $this->assertTrue($date->isEmpty());
        $this->assertFalse($date->isRange());
    }
}
