<?php

namespace Notion\Test\Unit\Pages\PropertyItems;

use DateTimeImmutable;
use Notion\Pages\Properties\PropertyType;
use Notion\Pages\PropertyItems\CreatedTimePropertyItem;
use PHPUnit\Framework\TestCase;

class CreatedTimePropertyItemTest extends TestCase
{
    public function test_create(): void
    {
        $time = new DateTimeImmutable("2020-03-17T19:10:04.968Z");
        $item = CreatedTimePropertyItem::create($time, "time-id");

        $this->assertSame("time-id", $item->metadata()->id);
        $this->assertSame(PropertyType::CreatedTime, $item->metadata()->type);
        $this->assertSame($time, $item->createdTime);
    }

    public function test_from_array(): void
    {
        $array = [
            "object" => "property_item",
            "id" => "KpQq",
            "type" => "created_time",
            "created_time" => "2020-03-17T19:10:04.968000+00:00",
        ];

        $item = CreatedTimePropertyItem::fromArray($array);

        $this->assertSame("KpQq", $item->metadata()->id);
        $this->assertSame($array, $item->toArray());
    }
}
