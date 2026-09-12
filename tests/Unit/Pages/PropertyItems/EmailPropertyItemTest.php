<?php

namespace Notion\Test\Unit\Pages\PropertyItems;

use Notion\Pages\Properties\PropertyType;
use Notion\Pages\PropertyItems\EmailPropertyItem;
use PHPUnit\Framework\TestCase;

class EmailPropertyItemTest extends TestCase
{
    public function test_create(): void
    {
        $item = EmailPropertyItem::create("test@example.com", "email-id");

        $this->assertSame("email-id", $item->metadata()->id);
        $this->assertSame(PropertyType::Email, $item->metadata()->type);
        $this->assertSame("test@example.com", $item->email);
        $this->assertFalse($item->isEmpty());
    }

    public function test_from_array(): void
    {
        $array = [
            "object" => "property_item",
            "id" => "KpQq",
            "type" => "email",
            "email" => "hello@test.com",
        ];

        $item = EmailPropertyItem::fromArray($array);

        $this->assertSame("KpQq", $item->metadata()->id);
        $this->assertSame("hello@test.com", $item->email);
        $this->assertSame($array, $item->toArray());
    }

    public function test_empty(): void
    {
        $item = EmailPropertyItem::create(null);
        $this->assertTrue($item->isEmpty());
    }
}
