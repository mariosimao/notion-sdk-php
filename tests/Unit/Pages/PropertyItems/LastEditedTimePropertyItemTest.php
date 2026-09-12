<?php

namespace Notion\Test\Unit\Pages\PropertyItems;

use DateTimeImmutable;
use Notion\Pages\Properties\PropertyType;
use Notion\Pages\PropertyItems\LastEditedTimePropertyItem;
use PHPUnit\Framework\TestCase;

class LastEditedTimePropertyItemTest extends TestCase
{
    public function test_create(): void
    {
        $time = new DateTimeImmutable("2020-03-17T19:10:04.968Z");
        $item = LastEditedTimePropertyItem::create($time, "let-id");

        $this->assertSame("let-id", $item->metadata()->id);
        $this->assertSame(PropertyType::LastEditedTime, $item->metadata()->type);
        $this->assertSame($time, $item->lastEditedTime);
    }

    public function test_from_array(): void
    {
        $array = [
            "object" => "property_item",
            "id" => "let-id",
            "type" => "last_edited_time",
            "last_edited_time" => "2020-03-17T19:10:04.968000+00:00",
        ];

        $item = LastEditedTimePropertyItem::fromArray($array);

        $this->assertSame("let-id", $item->metadata()->id);
        $this->assertSame($array, $item->toArray());
    }
}
