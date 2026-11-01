<?php

namespace Notion\Test\Unit\Blocks;

use Notion\Blocks\BlockFactory;
use Notion\Exceptions\BlockException;
use Notion\Blocks\NumberedListFormat;
use Notion\Blocks\NumberedListItem;
use Notion\Common\Color;
use Notion\Common\Date;
use Notion\Common\RichText;
use PHPUnit\Framework\TestCase;

class NumberedListItemTest extends TestCase
{
    public function test_create_empty_item(): void
    {
        $item = NumberedListItem::create();

        $this->assertEmpty($item->text);
        $this->assertEmpty($item->children);
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
            "in_trash"         => false,
            "has_children"     => false,
            "type"             => "numbered_list_item",
            "numbered_list_item"        => [
                "rich_text" => [
                    [
                        "plain_text"  => "Notion items ",
                        "href"        => null,
                        "type"        => "text",
                        "text"        => [
                            "content" => "Notion items ",
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

        $this->assertCount(2, $item->text);
        $this->assertEmpty($item->children);
        $this->assertEquals("Notion items rock!", $item->toString());
        $this->assertFalse($item->metadata()->inTrash);

        $this->assertEquals($item, BlockFactory::fromArray($array));
    }

    public function test_error_on_wrong_type(): void
    {
        $this->expectException(BlockException::class);
        $array = [
            "object"           => "block",
            "id"               => "04a13895-f072-4814-8af7-cd11af127040",
            "created_time"     => "2021-10-18T17:09:00.000Z",
            "last_edited_time" => "2021-10-18T17:09:00.000Z",
            "in_trash"         => false,
            "has_children"     => false,
            "type"             => "wrong-type",
            "numbered_list_item"        => [
                "rich_text"     => [],
                "children" => [],
            ],
        ];

        NumberedListItem::fromArray($array);
    }

    public function test_transform_in_array(): void
    {
        $i = NumberedListItem::fromString("Simple item");

        $expected = [
            "object"           => "block",
            "created_time"     => $i->metadata()->createdTime->format(Date::FORMAT),
            "last_edited_time" => $i->metadata()->lastEditedTime->format(Date::FORMAT),
            "in_trash"         => false,
            "has_children"     => false,
            "type"             => "numbered_list_item",
            "numbered_list_item" => [
                "rich_text" => [[
                    "plain_text"  => "Simple item",
                    "href"        => null,
                    "type"        => "text",
                    "text"        => [
                        "content" => "Simple item",
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
                "color" => "default",
                "children" => [],
            ],
        ];

        $this->assertEquals($expected, $i->toArray());
    }

    public function test_replace_text(): void
    {
        $oldItem = NumberedListItem::fromString("This is an old item");

        $newItem = $oldItem->changeText(
            RichText::fromString("This is a "),
            RichText::fromString("new item"),
        );

        $this->assertEquals("This is an old item", $oldItem->toString());
        $this->assertEquals("This is a new item", $newItem->toString());
    }

    public function test_add_text(): void
    {
        $oldItem = NumberedListItem::fromString("A item");

        $newItem = $oldItem->addText(
            RichText::fromString(" can be extended.")
        );

        $this->assertEquals("A item", $oldItem->toString());
        $this->assertEquals("A item can be extended.", $newItem->toString());
    }

    public function test_replace_children(): void
    {
        $nested1 = NumberedListItem::fromString("Nested item 1");
        $nested2 = NumberedListItem::fromString("Nested item 2");
        $item = NumberedListItem::fromString("Simple item.")->changeChildren($nested1, $nested2);

        $this->assertCount(2, $item->children);
        $this->assertEquals($nested1, $item->children[0]);
        $this->assertEquals($nested2, $item->children[1]);
    }

    public function test_add_child(): void
    {
        $item = NumberedListItem::fromString("Simple item.");
        $nested = NumberedListItem::fromString("Nested item");
        $item = $item->addChild($nested);

        $this->assertCount(1, $item->children);
        $this->assertEquals($nested, $item->children[0]);
    }

    public function test_change_color(): void
    {
        $block = NumberedListItem::fromString("Hello World!")->changeColor(Color::Red);

        $this->assertSame(Color::Red, $block->color);
    }

    public function test_list_metadata_defaults_to_null(): void
    {
        $item = NumberedListItem::fromString("Item");

        $this->assertNull($item->listStartIndex);
        $this->assertNull($item->listFormat);
        $this->assertArrayNotHasKey("list_start_index", self::itemArray($item));
        $this->assertArrayNotHasKey("list_format", self::itemArray($item));
    }

    public function test_create_from_array_with_list_metadata(): void
    {
        $array = [
            "object"           => "block",
            "id"               => "04a13895-f072-4814-8af7-cd11af127040",
            "created_time"     => "2021-10-18T17:09:00.000Z",
            "last_edited_time" => "2021-10-18T17:09:00.000Z",
            "in_trash"         => false,
            "has_children"     => false,
            "type"             => "numbered_list_item",
            "numbered_list_item" => [
                "rich_text"        => [],
                "color"            => "default",
                "list_start_index" => 3,
                "list_format"      => "roman",
            ],
        ];

        $item = NumberedListItem::fromArray($array);

        $this->assertSame(3, $item->listStartIndex);
        $this->assertSame(NumberedListFormat::Roman, $item->listFormat);
        $this->assertSame(3, self::itemArray($item)["list_start_index"]);
        $this->assertSame("roman", self::itemArray($item)["list_format"]);
    }

    public function test_unknown_list_format_is_ignored(): void
    {
        $array = [
            "object"           => "block",
            "id"               => "04a13895-f072-4814-8af7-cd11af127040",
            "created_time"     => "2021-10-18T17:09:00.000Z",
            "last_edited_time" => "2021-10-18T17:09:00.000Z",
            "in_trash"         => false,
            "has_children"     => false,
            "type"             => "numbered_list_item",
            "numbered_list_item" => [
                "rich_text"   => [],
                "list_format" => "unknown",
            ],
        ];

        $this->assertNull(NumberedListItem::fromArray($array)->listFormat);
    }

    public function test_change_list_metadata(): void
    {
        $item = NumberedListItem::fromString("Item")
            ->changeListStartIndex(4)
            ->changeListFormat(NumberedListFormat::Letters);

        $this->assertSame(4, $item->listStartIndex);
        $this->assertSame(NumberedListFormat::Letters, $item->listFormat);

        $expected = [
            "rich_text"        => self::itemArray($item)["rich_text"],
            "color"            => "default",
            "children"         => [],
            "list_start_index" => 4,
            "list_format"      => "letters",
        ];
        $this->assertSame($expected, self::itemArray($item));
    }

    public function test_list_metadata_is_preserved(): void
    {
        $item = NumberedListItem::fromString("Item")
            ->changeListStartIndex(2)
            ->changeListFormat(NumberedListFormat::Numbers)
            ->changeText(RichText::fromString("Changed"))
            ->addText(RichText::fromString(" text"))
            ->changeColor(Color::Blue)
            ->addChild(NumberedListItem::fromString("Child"))
            ->changeChildren()
            ->delete();

        $this->assertInstanceOf(NumberedListItem::class, $item);
        $this->assertSame(2, $item->listStartIndex);
        $this->assertSame(NumberedListFormat::Numbers, $item->listFormat);
    }

    public function test_remove_list_metadata(): void
    {
        $item = NumberedListItem::fromString("Item")
            ->changeListStartIndex(2)
            ->changeListFormat(NumberedListFormat::Roman)
            ->changeListStartIndex(null)
            ->changeListFormat(null);

        $this->assertNull($item->listStartIndex);
        $this->assertNull($item->listFormat);
    }

    /** @return array<string, mixed> */
    private static function itemArray(NumberedListItem $item): array
    {
        /** @var array{numbered_list_item: array<string, mixed>} $array */
        $array = $item->toArray();

        return $array["numbered_list_item"];
    }
}
