<?php

namespace Notion\Test\Unit\Pages\Properties;

use DateTimeImmutable;
use Notion\Pages\Properties\CreatedTime;
use Notion\Pages\Properties\PropertyFactory;
use Notion\Pages\Properties\PropertyType;
use PHPUnit\Framework\TestCase;

class CreatedTimeTest extends TestCase
{
    public function test_create(): void
    {
        $date = new DateTimeImmutable("2021-01-01T00:00:00.000000Z");
        $time = CreatedTime::create($date);

        $this->assertEquals(PropertyType::CreatedTime, $time->metadata()->type);
        $this->assertEquals($date, $time->time);
    }

    public function test_change_time(): void
    {
        $date1 = new DateTimeImmutable("2021-01-01T00:00:00.000000Z");
        $date2 = new DateTimeImmutable("2022-01-01T00:00:00.000000Z");

        $time = CreatedTime::create($date1)->changeTime($date2);

        $this->assertEquals($date2, $time->time);
    }

    public function test_array_conversion(): void
    {
        $array = [
            "id" => "abc",
            "type" => "created_time",
            "created_time" => "2021-01-01T00:00:00.000000Z",
        ];

        $time = CreatedTime::fromArray($array);
        $fromFactory = PropertyFactory::fromArray($array);

        $this->assertEquals($array, $time->toArray());
        $this->assertEquals($array, $fromFactory->toArray());
    }
}
