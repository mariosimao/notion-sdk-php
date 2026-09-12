<?php

namespace Notion\Test\Unit\Pages\PropertyItems;

use Notion\DataSources\Properties\StatusOption;
use Notion\Pages\Properties\PropertyType;
use Notion\Pages\PropertyItems\StatusPropertyItem;
use PHPUnit\Framework\TestCase;

class StatusPropertyItemTest extends TestCase
{
    public function test_create(): void
    {
        $option = StatusOption::fromName("In Progress");
        $item = StatusPropertyItem::create($option, "status-id");

        $this->assertSame("status-id", $item->metadata()->id);
        $this->assertSame(PropertyType::Status, $item->metadata()->type);
        $this->assertSame("In Progress", $item->option?->name);
        $this->assertFalse($item->isEmpty());
    }

    public function test_from_array(): void
    {
        $array = [
            "object" => "property_item",
            "id" => "status-id",
            "type" => "status",
            "status" => [
                "name" => "Done",
                "id" => "status-uuid",
                "color" => "green",
            ],
        ];

        $item = StatusPropertyItem::fromArray($array);

        $this->assertSame("status-id", $item->metadata()->id);
        $this->assertSame("Done", $item->option?->name);
        $this->assertSame($array, $item->toArray());
    }

    public function test_empty(): void
    {
        $item = StatusPropertyItem::create(null);
        $this->assertTrue($item->isEmpty());
    }
}
