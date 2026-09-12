<?php

namespace Notion\Test\Unit\Pages\PropertyItems;

use Notion\DataSources\Properties\SelectOption;
use Notion\Pages\Properties\PropertyType;
use Notion\Pages\PropertyItems\SelectPropertyItem;
use PHPUnit\Framework\TestCase;

class SelectPropertyItemTest extends TestCase
{
    public function test_create(): void
    {
        $option = SelectOption::fromName("Option 1");
        $item = SelectPropertyItem::create($option, "sel-id");

        $this->assertSame("sel-id", $item->metadata()->id);
        $this->assertSame(PropertyType::Select, $item->metadata()->type);
        $this->assertSame("Option 1", $item->option?->name);
        $this->assertFalse($item->isEmpty());
    }

    public function test_from_array(): void
    {
        $array = [
            "object" => "property_item",
            "id" => "%7CtzR",
            "type" => "select",
            "select" => [
                "name" => "Option 1",
                "id" => "64190ec9-e963-47cb-bc37-6a71d6b71206",
                "color" => "orange",
            ],
        ];

        $item = SelectPropertyItem::fromArray($array);

        $this->assertSame("%7CtzR", $item->metadata()->id);
        $this->assertSame("Option 1", $item->option?->name);
        $this->assertSame($array, $item->toArray());
    }

    public function test_null_option(): void
    {
        $item = SelectPropertyItem::create(null);
        $this->assertTrue($item->isEmpty());
    }
}
