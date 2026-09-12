<?php

namespace Notion\Test\Unit\Pages\PropertyItems;

use Notion\Pages\Properties\PropertyType;
use Notion\Pages\PropertyItems\NumberPropertyItem;
use PHPUnit\Framework\TestCase;

class NumberPropertyItemTest extends TestCase
{
    public function test_create(): void
    {
        $item = NumberPropertyItem::create(42, "num-id");

        $this->assertSame("num-id", $item->metadata()->id);
        $this->assertSame(PropertyType::Number, $item->metadata()->type);
        $this->assertSame(42, $item->number);
        $this->assertFalse($item->isEmpty());
    }

    public function test_from_array(): void
    {
        $array = [
            "object" => "property_item",
            "id" => "XpXf",
            "type" => "number",
            "number" => 1234,
        ];

        $item = NumberPropertyItem::fromArray($array);

        $this->assertSame("XpXf", $item->metadata()->id);
        $this->assertSame(1234, $item->number);
        $this->assertSame($array, $item->toArray());
    }

    public function test_empty(): void
    {
        $item = NumberPropertyItem::create(null);
        $this->assertTrue($item->isEmpty());
    }
}
