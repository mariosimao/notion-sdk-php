<?php

namespace Notion\Test\Unit\Pages\PropertyItems;

use Notion\Pages\Properties\PropertyType;
use Notion\Pages\PropertyItems\PhoneNumberPropertyItem;
use PHPUnit\Framework\TestCase;

class PhoneNumberPropertyItemTest extends TestCase
{
    public function test_create(): void
    {
        $item = PhoneNumberPropertyItem::create("415-000-1111", "phone-id");

        $this->assertSame("phone-id", $item->metadata()->id);
        $this->assertSame(PropertyType::PhoneNumber, $item->metadata()->type);
        $this->assertSame("415-000-1111", $item->phoneNumber);
        $this->assertFalse($item->isEmpty());
    }

    public function test_from_array(): void
    {
        $array = [
            "object" => "property_item",
            "id" => "KpQq",
            "type" => "phone_number",
            "phone_number" => "415-000-1111",
        ];

        $item = PhoneNumberPropertyItem::fromArray($array);

        $this->assertSame("KpQq", $item->metadata()->id);
        $this->assertSame("415-000-1111", $item->phoneNumber);
        $this->assertSame($array, $item->toArray());
    }

    public function test_empty(): void
    {
        $item = PhoneNumberPropertyItem::create(null);
        $this->assertTrue($item->isEmpty());
    }
}
