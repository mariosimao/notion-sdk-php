<?php

namespace Notion\Test\Unit\Pages\PropertyItems;

use Notion\Pages\Properties\PropertyType;
use Notion\Pages\PropertyItems\UniqueIdPropertyItem;
use PHPUnit\Framework\TestCase;

class UniqueIdPropertyItemTest extends TestCase
{
    public function test_create(): void
    {
        $item = UniqueIdPropertyItem::create(42, "TASK", "uid-id");

        $this->assertSame("uid-id", $item->metadata()->id);
        $this->assertSame(PropertyType::UniqueId, $item->metadata()->type);
        $this->assertSame(42, $item->number);
        $this->assertSame("TASK", $item->prefix);
    }

    public function test_from_array(): void
    {
        $array = [
            "object" => "property_item",
            "id" => "uid-id",
            "type" => "unique_id",
            "unique_id" => [
                "number" => 123,
                "prefix" => "BUG",
            ],
        ];

        $item = UniqueIdPropertyItem::fromArray($array);

        $this->assertSame("uid-id", $item->metadata()->id);
        $this->assertSame(123, $item->number);
        $this->assertSame("BUG", $item->prefix);
        $this->assertSame($array, $item->toArray());
    }
}
