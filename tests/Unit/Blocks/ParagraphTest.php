<?php

namespace Notion\Test\Unit\Blocks;

use Notion\Blocks\BlockFactory;
use Notion\Exceptions\BlockException;
use Notion\Blocks\Paragraph;
use Notion\Common\Color;
use Notion\Common\Date;
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
            "archived"         => false,
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
        $this->assertFalse($paragraph->metadata()->archived);

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
            "archived"         => false,
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
            "archived"         => false,
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
}
