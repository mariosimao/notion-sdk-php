<?php

namespace Notion\Test\Unit\Blocks;

use Notion\Blocks\BlockFactory;
use Notion\Blocks\Exceptions\BlockException;
use Notion\Blocks\Heading3;
use Notion\Common\Date;
use Notion\Common\RichText;
use Notion\NotionException;
use PHPUnit\Framework\TestCase;

class Heading3Test extends TestCase
{
    public function test_create_empty_heading(): void
    {
        $heading = Heading3::create();

        $this->assertEmpty($heading->text);
    }

    public function test_create_from_string(): void
    {
        $heading = Heading3::fromString("Dummy heading.");

        $this->assertEquals("Dummy heading.", $heading->toString());
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
            "type"             => "heading_3",
            "heading_3"        => [
                "rich_text" => [
                    [
                        "plain_text"  => "Notion headings ",
                        "href"        => null,
                        "type"        => "rich_text",
                        "rich_text"        => [
                            "content" => "Notion headings ",
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
                        "type"        => "rich_text",
                        "rich_text"        => [
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

        $heading = Heading3::fromArray($array);

        $this->assertCount(2, $heading->text);
        $this->assertEquals("Notion headings rock!", $heading->toString());
        $this->assertFalse($heading->metadata()->archived);

        $this->assertEquals($heading, BlockFactory::fromArray($array));
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
            "heading_3"        => [
                "rich_text"     => [],
            ],
        ];

        Heading3::fromArray($array);
    }

    public function test_transform_in_array(): void
    {
        $h = Heading3::fromString("Simple heading");

        $expected = [
            "object"           => "block",
            "created_time"     => $h->metadata()->createdTime->format(Date::FORMAT),
            "last_edited_time" => $h->metadata()->lastEditedTime->format(Date::FORMAT),
            "archived"         => false,
            "has_children"      => false,
            "type"             => "heading_3",
            "heading_3"        => [
                "rich_text" => [[
                    "plain_text"  => "Simple heading",
                    "href"        => null,
                    "type"        => "text",
                    "text"        => [
                        "content" => "Simple heading",
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
            ],
        ];

        $this->assertEquals($expected, $h->toArray());
    }

    public function test_replace_text(): void
    {
        $oldHeading = Heading3::fromString("This is an old heading");

        $newHeading = $oldHeading->changeText([
            RichText::createText("This is a "),
            RichText::createText("new heading"),
        ]);

        $this->assertEquals("This is an old heading", $oldHeading->toString());
        $this->assertEquals("This is a new heading", $newHeading->toString());
    }

    public function test_add_text(): void
    {
        $oldHeading = Heading3::fromString("A heading");

        $newHeading = $oldHeading->addText(
            RichText::createText(" can be extended.")
        );

        $this->assertEquals("A heading", $oldHeading->toString());
        $this->assertEquals("A heading can be extended.", $newHeading->toString());
    }

    public function test_no_children_support(): void
    {
        $block = Heading3::create();

        $this->expectException(NotionException::class);
        /** @psalm-suppress UnusedMethodCall */
        $block->changeChildren();
    }

    public function test_array_for_update_operations(): void
    {
        $block = Heading3::create();

        $array = $block->toUpdateArray();

        $this->assertCount(2, $array);
    }
}
