<?php

namespace Notion\Test\Unit\Blocks;

use Notion\Blocks\NumberedListItem;
use Notion\Common\RichText;
use PHPUnit\Framework\TestCase;

class NumberedListItemTest extends TestCase
{
    public function test_create_empty_item(): void
    {
        $item = NumberedListItem::create();

        $this->assertEmpty($item->text());
        $this->assertEmpty($item->children());
    }

    public function test_create_from_string(): void
    {
        $item = NumberedListItem::fromString("Dummy item.");

        $this->assertEquals("Dummy item.", $item->toString());
    }

    public function test_create_from_array(): void
    {
        $array = [
            "object"           => "block",
            "id"               => "04a13895-f072-4814-8af7-cd11af127040",
            "created_time"     => "2021-10-18T17:09:00.000Z",
            "last_edited_time" => "2021-10-18T17:09:00.000Z",
            "archived"         => false,
            "has_children"     => false,
            "type"             => "numbered_list_item",
            "numbered_list_item"        => [
                "text" => [
                    [
                        "plain_text"  => "Notion items ",
                        "href"        => null,
                        "type"        => "text",
                        "text"        => [
                            "content" => "Notion items ",
                            "link" => null,
                        ],
                        "annotations" => [
                            "bold"          => false,
                            "italic"        => false,
                            "strikethrough" => false,
                            "underline"     => false,
                            "code"          => false,
                            "color"         => "default",
                        ],
                    ],
                    [
                        "plain_text"  => "rock!",
                        "href"        => null,
                        "type"        => "text",
                        "text"        => [
                            "content" => "rock!",
                            "link" => null,
                        ],
                        "annotations" => [
                            "bold"          => true,
                            "italic"        => false,
                            "strikethrough" => false,
                            "underline"     => false,
                            "code"          => false,
                            "color"         => "red",
                        ],
                    ],
                ],
                "children" => [],
            ],
        ];

        $item = NumberedListItem::fromArray($array);

        $this->assertCount(2, $item->text());
        $this->assertEmpty($item->children());
        $this->assertEquals("Notion items rock!", $item->toString());
        $this->assertFalse($item->block()->archived());
    }

    public function test_error_on_wrong_type(): void
    {
        $this->expectException(\Exception::class);
        $array = [
            "object"           => "block",
            "id"               => "04a13895-f072-4814-8af7-cd11af127040",
            "created_time"     => "2021-10-18T17:09:00.000Z",
            "last_edited_time" => "2021-10-18T17:09:00.000Z",
            "archived"         => false,
            "has_children"     => false,
            "type"             => "wrong-type",
            "numbered_list_item"        => [
                "text"     => [],
                "children" => [],
            ],
        ];

        $item = NumberedListItem::fromArray($array);
    }

    public function test_transform_in_array(): void
    {
        $i = NumberedListItem::fromString("Simple item");

        $expected = [
            "object"           => "block",
            "created_time"     => $i->block()->createdTime()->format(DATE_ISO8601),
            "last_edited_time" => $i->block()->lastEditedType()->format(DATE_ISO8601),
            "archived"         => false,
            "has_children"     => false,
            "type"             => "numbered_list_item",
            "numbered_list_item" => [
                "text" => [[
                    "plain_text"  => "Simple item",
                    "href"        => null,
                    "type"        => "text",
                    "text"        => [
                        "content" => "Simple item",
                        "link" => null,
                    ],
                    "annotations" => [
                        "bold"          => false,
                        "italic"        => false,
                        "strikethrough" => false,
                        "underline"     => false,
                        "code"          => false,
                        "color"         => "default",
                    ],
                ]],
                "children" => [],
            ],
        ];

        $this->assertEquals($expected, $i->toArray());
    }

    public function test_replace_text(): void
    {
        $oldItem = NumberedListItem::fromString("This is an old item");

        $newItem = $oldItem->withText(
            RichText::createText("This is a "),
            RichText::createText("new item"),
        );

        $this->assertEquals("This is an old item", $oldItem->toString());
        $this->assertEquals("This is a new item", $newItem->toString());
    }

    public function test_append_text(): void
    {
        $oldItem = NumberedListItem::fromString("A item");

        $newItem = $oldItem->appendText(
            RichText::createText(" can be extended.")
        );

        $this->assertEquals("A item", $oldItem->toString());
        $this->assertEquals("A item can be extended.", $newItem->toString());
    }

    public function test_replace_children(): void
    {
        $item = NumberedListItem::fromString("Simple item.")->withChildren(
            NumberedListItem::fromString("Nested item 1"),
            NumberedListItem::fromString("Nested item 2"),
        );

        $this->assertCount(2, $item->children());
        $this->assertEquals("Nested item 1", $item->children()[0]->toString());
        $this->assertEquals("Nested item 2", $item->children()[1]->toString());
    }

    public function test_append_child(): void
    {
        $item = NumberedListItem::fromString("Simple item.");
        $item = $item->appendChild(NumberedListItem::fromString("Nested item"));

        $this->assertCount(1, $item->children());
        $this->assertEquals("Nested item", $item->children()[0]->toString());
    }
}
