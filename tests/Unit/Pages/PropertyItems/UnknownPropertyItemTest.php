<?php

namespace Notion\Test\Unit\Pages\PropertyItems;

use Notion\Pages\Properties\PropertyType;
use Notion\Pages\PropertyItems\UnknownPropertyItem;
use PHPUnit\Framework\TestCase;

class UnknownPropertyItemTest extends TestCase
{
    public function test_create(): void
    {
        $item = UnknownPropertyItem::create("unk-id", "some_future_type", ["key" => "val"]);

        $this->assertSame("unk-id", $item->metadata()->id);
        $this->assertSame(PropertyType::Unknown, $item->metadata()->type);
        $this->assertSame(["key" => "val"], $item->toArray());
    }

    public function test_from_array(): void
    {
        $array = [
            "object" => "property_item",
            "id" => "unk-id",
            "type" => "custom_type",
            "custom_type" => "something",
        ];

        $item = UnknownPropertyItem::fromArray($array);

        $this->assertSame("unk-id", $item->metadata()->id);
        $this->assertSame($array, $item->toArray());
    }
}
