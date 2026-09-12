<?php

namespace Notion\Test\Unit\Pages\PropertyItems;

use Notion\Common\RichText;
use Notion\Pages\Properties\PropertyType;
use Notion\Pages\PropertyItems\RichTextPropertyItem;
use PHPUnit\Framework\TestCase;

class RichTextPropertyItemTest extends TestCase
{
    public function test_create(): void
    {
        $text = RichText::fromString("Hello world");
        $item = RichTextPropertyItem::create($text, "rich-id");

        $this->assertSame("rich-id", $item->metadata()->id);
        $this->assertSame(PropertyType::RichText, $item->metadata()->type);
        $this->assertSame("Hello world", $item->richText->plainText);
    }

    public function test_from_array(): void
    {
        $array = [
            "object" => "property_item",
            "id" => "NVv^",
            "type" => "rich_text",
            "rich_text" => [
                "plain_text" => "Hello world",
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
                "text" => ["content" => "Hello world"],
            ],
        ];

        $item = RichTextPropertyItem::fromArray($array);

        $this->assertSame("NVv^", $item->metadata()->id);
        $this->assertSame("Hello world", $item->richText->plainText);
        $this->assertSame($array, $item->toArray());
    }
}
