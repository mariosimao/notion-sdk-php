<?php

namespace Notion\Test\Unit\Pages\PropertyItems;

use Notion\Pages\Properties\PropertyType;
use Notion\Pages\PropertyItems\RelationPropertyItem;
use PHPUnit\Framework\TestCase;

class RelationPropertyItemTest extends TestCase
{
    public function test_create(): void
    {
        $item = RelationPropertyItem::create("page-uuid-123", "rel-id");

        $this->assertSame("rel-id", $item->metadata()->id);
        $this->assertSame(PropertyType::Relation, $item->metadata()->type);
        $this->assertSame("page-uuid-123", $item->pageId);
    }

    public function test_from_array(): void
    {
        $array = [
            "object" => "property_item",
            "id" => "vYdV",
            "type" => "relation",
            "relation" => [
                "id" => "535c3fb2-95e6-4b37-a696-036e5eac5cf6",
            ],
        ];

        $item = RelationPropertyItem::fromArray($array);

        $this->assertSame("vYdV", $item->metadata()->id);
        $this->assertSame("535c3fb2-95e6-4b37-a696-036e5eac5cf6", $item->pageId);
        $this->assertSame($array, $item->toArray());
    }
}
