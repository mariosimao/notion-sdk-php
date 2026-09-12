<?php

namespace Notion\Test\Unit\Pages\PropertyItems;

use Notion\DataSources\Properties\SelectOption;
use Notion\Pages\Properties\PropertyType;
use Notion\Pages\PropertyItems\MultiSelectPropertyItem;
use PHPUnit\Framework\TestCase;

class MultiSelectPropertyItemTest extends TestCase
{
    public function test_create(): void
    {
        $option = SelectOption::fromName("Tag 1");
        $item = MultiSelectPropertyItem::create([$option], "ms-id");

        $this->assertSame("ms-id", $item->metadata()->id);
        $this->assertSame(PropertyType::MultiSelect, $item->metadata()->type);
        $this->assertCount(1, $item->options);
        $this->assertFalse($item->isEmpty());
    }

    public function test_from_array(): void
    {
        $array = [
            "object" => "property_item",
            "id" => "z%7D%5C%3C",
            "type" => "multi_select",
            "multi_select" => [
                [
                    "id" => "91e6959e-7690-4f55-b8dd-d3da9debac45",
                    "name" => "A",
                    "color" => "orange",
                ],
            ],
        ];

        $item = MultiSelectPropertyItem::fromArray($array);

        $this->assertSame("z%7D%5C%3C", $item->metadata()->id);
        $this->assertCount(1, $item->options);
        $this->assertSame("A", $item->options[0]->name);
        $this->assertEquals([
            "object" => "property_item",
            "id" => "z%7D%5C%3C",
            "type" => "multi_select",
            "multi_select" => [
                [
                    "id" => "91e6959e-7690-4f55-b8dd-d3da9debac45",
                    "name" => "A",
                    "color" => "orange",
                ],
            ],
        ], $item->toArray());
    }

    public function test_empty(): void
    {
        $item = MultiSelectPropertyItem::create([]);
        $this->assertTrue($item->isEmpty());
    }
}
