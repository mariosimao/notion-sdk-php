<?php

namespace Notion\Test\Unit\Blocks;

use Notion\Blocks\BlockFactory;
use Notion\Exceptions\BlockException;
use Notion\Blocks\Paragraph;
use Notion\Common\Color;
use Notion\Common\Date;
use Notion\Common\Emoji;
use Notion\Common\File;
use Notion\Common\RichText;
use PHPUnit\Framework\TestCase;

class ParagraphTest extends TestCase
{
    public function test_create_empty_paragraph(): void
    {
        $paragraph = Paragraph::create();

        $this->assertEmpty($paragraph->text);
        $this->assertEmpty($paragraph->children);
    }

    public function test_create_from_string(): void
    {
        $paragraph = Paragraph::fromString("Dummy paragraph.");

        $this->assertEquals("Dummy paragraph.", $paragraph->toString());
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
            "type"             => "paragraph",
            "paragraph"        => [
                "rich_text" => [
                    [
                        "plain_text"  => "Notion paragraphs ",
                        "href"        => null,
                        "type"        => "text",
                        "text"        => [
                            "content" => "Notion paragraphs ",
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
                "color" => "green",
            ],
        ];

        $paragraph = Paragraph::fromArray($array);

        $this->assertCount(2, $paragraph->text);
        $this->assertEmpty($paragraph->children);
        $this->assertEquals("Notion paragraphs rock!", $paragraph->toString());
        $this->assertFalse($paragraph->metadata()->inTrash);

        $this->assertEquals($paragraph, BlockFactory::fromArray($array));
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
            "paragraph"        => [
                "rich_text"     => [],
                "children" => [],
            ],
        ];
        Paragraph::fromArray($array);
    }

    public function test_transform_in_array(): void
    {
        $p = Paragraph::fromString("Simple paragraph");

        $expected = [
            "object"           => "block",
            "created_time"     => $p->metadata()->createdTime->format(Date::FORMAT),
            "last_edited_time" => $p->metadata()->lastEditedTime->format(Date::FORMAT),
            "in_trash"         => false,
            "has_children"      => false,
            "type"             => "paragraph",
            "paragraph"        => [
                "rich_text" => [[
                    "plain_text"  => "Simple paragraph",
                    "href"        => null,
                    "type"        => "text",
                    "text"        => [
                        "content" => "Simple paragraph",
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
                "color" => "default",
            ],
        ];

        $this->assertEquals($expected, $p->toArray());
    }

    public function test_replace_text(): void
    {
        $oldParagraph = Paragraph::fromString("This is an old paragraph");

        $newParagraph = $oldParagraph->changeText([
            RichText::fromString("This is a "),
            RichText::fromString("new paragraph"),
        ]);

        $this->assertEquals("This is an old paragraph", $oldParagraph->toString());
        $this->assertEquals("This is a new paragraph", $newParagraph->toString());
    }

    public function test_add_text(): void
    {
        $oldParagraph = Paragraph::fromString("A paragraph");

        $newParagraph = $oldParagraph->addText(
            RichText::fromString(" can be extended.")
        );

        $this->assertEquals("A paragraph", $oldParagraph->toString());
        $this->assertEquals("A paragraph can be extended.", $newParagraph->toString());
    }

    public function test_replace_children(): void
    {
        $nested1 = Paragraph::fromString("Nested paragraph 1");
        $nested2 = Paragraph::fromString("Nested paragraph 2");
        $paragraph = Paragraph::fromString("Simple paragraph.")->changeChildren($nested1, $nested2);

        $this->assertCount(2, $paragraph->children);
        $this->assertEquals($nested1, $paragraph->children[0]);
        $this->assertEquals($nested2, $paragraph->children[1]);
    }

    public function test_add_child(): void
    {
        $paragraph = Paragraph::fromString("Simple paragraph.");
        $nested = Paragraph::fromString("Nested paragraph");
        $paragraph = $paragraph->addChild($nested);

        $this->assertCount(1, $paragraph->children);
        $this->assertEquals($nested, $paragraph->children[0]);
    }

    public function test_change_color(): void
    {
        $paragraph = Paragraph::fromString("Hello World!")->changeColor(Color::Red);

        $this->assertSame(Color::Red, $paragraph->color);
    }

    public function test_change_icon(): void
    {
        $paragraph = Paragraph::fromString("Overview")->changeIcon(Emoji::fromString("📋"));

        $this->assertSame("📋", $paragraph->icon?->emoji?->emoji);
        /** @psalm-var array{ paragraph: array<string, mixed> } $array */
        $array = $paragraph->toArray();
        $this->assertSame([ "type" => "emoji", "emoji" => "📋" ], $array["paragraph"]["icon"]);
    }

    public function test_change_icon_to_file(): void
    {
        $file = File::createExternal("https://example.com/icon.png");
        $paragraph = Paragraph::fromString("Overview")->changeIcon($file);

        $this->assertEquals($file, $paragraph->icon?->file);
    }

    public function test_remove_icon(): void
    {
        $paragraph = Paragraph::fromString("Overview")
            ->changeIcon(Emoji::fromString("📋"))
            ->removeIcon();

        $this->assertNull($paragraph->icon);
        /** @psalm-var array{ paragraph: array<string, mixed> } $array */
        $array = $paragraph->toArray();
        $this->assertArrayNotHasKey("icon", $array["paragraph"]);
    }

    public function test_icon_is_preserved(): void
    {
        $paragraph = Paragraph::fromString("Overview")
            ->changeIcon(Emoji::fromString("📋"))
            ->changeText([ RichText::fromString("Details") ])
            ->addText(RichText::fromString("!"))
            ->addChild(Paragraph::fromString("Content"))
            ->changeChildren(Paragraph::fromString("Content"))
            ->changeColor(Color::Red)
            ->delete();

        $this->assertInstanceOf(Paragraph::class, $paragraph);
        $this->assertSame("📋", $paragraph->icon?->emoji?->emoji);
    }

    public function test_icon_from_array(): void
    {
        $base = [
            "object"           => "block",
            "id"               => "04a13895-f072-4814-8af7-cd11af127040",
            "created_time"     => "2026-03-25T17:09:00.000000Z",
            "last_edited_time" => "2026-03-25T17:09:00.000000Z",
            "in_trash"         => false,
            "has_children"     => false,
            "type"             => "paragraph",
        ];

        $emoji = Paragraph::fromArray($base + [
            "paragraph" => [ "rich_text" => [], "icon" => [ "type" => "emoji", "emoji" => "📋" ] ],
        ]);
        $file = Paragraph::fromArray($base + [
            "paragraph" => [
                "rich_text" => [],
                "icon" => [ "type" => "external", "external" => [ "url" => "https://example.com/icon.png" ] ],
            ],
        ]);
        $unsupported = Paragraph::fromArray($base + [
            "paragraph" => [ "rich_text" => [], "icon" => [ "type" => "icon", "icon" => [ "name" => "star" ] ] ],
        ]);
        $null = Paragraph::fromArray($base + [
            "paragraph" => [ "rich_text" => [], "icon" => null ],
        ]);

        $this->assertSame("📋", $emoji->icon?->emoji?->emoji);
        $this->assertSame("https://example.com/icon.png", $file->icon?->file?->url);
        $this->assertNull($unsupported->icon);
        $this->assertNull($null->icon);
    }
}
