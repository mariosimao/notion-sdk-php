<?php

namespace Notion\Test\Unit\Pages\PropertyItems;

use Notion\Pages\Properties\PropertyType;
use Notion\Pages\PropertyItems\UrlPropertyItem;
use PHPUnit\Framework\TestCase;

class UrlPropertyItemTest extends TestCase
{
    public function test_create(): void
    {
        $item = UrlPropertyItem::create("https://notion.so", "url-id");

        $this->assertSame("url-id", $item->metadata()->id);
        $this->assertSame(PropertyType::Url, $item->metadata()->type);
        $this->assertSame("https://notion.so", $item->url);
        $this->assertFalse($item->isEmpty());
    }

    public function test_from_array(): void
    {
        $array = [
            "object" => "property_item",
            "id" => "KpQq",
            "type" => "url",
            "url" => "https://notion.com/notiondevs",
        ];

        $item = UrlPropertyItem::fromArray($array);

        $this->assertSame("KpQq", $item->metadata()->id);
        $this->assertSame("https://notion.com/notiondevs", $item->url);
        $this->assertSame($array, $item->toArray());
    }

    public function test_empty(): void
    {
        $item = UrlPropertyItem::create(null);
        $this->assertTrue($item->isEmpty());
    }
}
