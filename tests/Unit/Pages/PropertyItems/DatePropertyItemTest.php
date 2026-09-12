<?php

namespace Notion\Test\Unit\Pages\PropertyItems;

use DateTimeImmutable;
use Notion\Common\Date;
use Notion\Pages\Properties\PropertyType;
use Notion\Pages\PropertyItems\DatePropertyItem;
use PHPUnit\Framework\TestCase;

class DatePropertyItemTest extends TestCase
{
    public function test_create(): void
    {
        $date = Date::create(new DateTimeImmutable("2021-05-11T11:00:00.000Z"));
        $item = DatePropertyItem::create($date, "date-id");

        $this->assertSame("date-id", $item->metadata()->id);
        $this->assertSame(PropertyType::Date, $item->metadata()->type);
        $this->assertSame($date, $item->date);
        $this->assertFalse($item->isEmpty());
    }

    public function test_from_array(): void
    {
        $array = [
            "object" => "property_item",
            "id" => "i%3Ahj",
            "type" => "date",
            "date" => [
                "start" => "2021-05-11T11:00:00.000-04:00",
                "end" => null,
                "time_zone" => null,
            ],
        ];

        $item = DatePropertyItem::fromArray($array);

        $this->assertSame("i%3Ahj", $item->metadata()->id);
        $this->assertNotNull($item->date);
        $this->assertSame("2021-05-11T11:00:00.000-04:00", $item->date->start->format("Y-m-d\TH:i:s.vP"));
        $this->assertEquals([
            "object" => "property_item",
            "id" => "i%3Ahj",
            "type" => "date",
            "date" => [
                "start" => "2021-05-11T11:00:00.000000-04:00",
                "end" => null,
            ],
        ], $item->toArray());
    }

    public function test_empty(): void
    {
        $item = DatePropertyItem::create(null);
        $this->assertTrue($item->isEmpty());
    }
}
