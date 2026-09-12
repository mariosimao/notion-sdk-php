<?php

namespace Notion\Test\Unit\Pages\PropertyItems;

use Notion\Common\RichText;
use Notion\Pages\Properties\PropertyType;
use Notion\Pages\PropertyItems\TitlePropertyItem;
use PHPUnit\Framework\TestCase;

class TitlePropertyItemTest extends TestCase
{
    public function test_create(): void
    {
        $text = RichText::fromString("Page Title");
        $item = TitlePropertyItem::create($text, "title-id");

        $this->assertSame("title-id", $item->metadata()->id);
        $this->assertSame(PropertyType::Title, $item->metadata()->type);
        $this->assertSame("Page Title", $item->title->plainText);
    }

    public function test_from_array(): void
    {
        $array = [
            "object" => "property_item",
            "id" => "title",
            "type" => "title",
            "title" => [
                "plain_text" => "Page Title",
                "href" => null,
                "annotations" => [
                    "bold" => false,
                    "italic" => false,
                    "strikethrough" => false,
                    "underline" => false,
                    "code" => false,
                    "color" => "default",
                ],
                "type" => "text",
                "text" => ["content" => "Page Title"],
            ],
        ];

        $item = TitlePropertyItem::fromArray($array);

        $this->assertSame("title", $item->metadata()->id);
        $this->assertSame("Page Title", $item->title->plainText);
        $this->assertSame($array, $item->toArray());
    }
}
