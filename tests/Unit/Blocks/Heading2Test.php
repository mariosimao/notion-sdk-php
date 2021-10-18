<?php

namespace Notion\Test\Blocks;

use Notion\Blocks\Heading2;
use Notion\Common\RichText;
use PHPUnit\Framework\TestCase;

class Heading2Test extends TestCase
{
    public function test_create_empty_heading(): void
    {
        $heading = Heading2::create();

        $this->assertEmpty($heading->text());
    }

    public function test_create_from_string(): void
    {
        $heading = Heading2::fromString("Dummy heading.");

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
            "type"             => "heading_2",
            "heading_2"        => [
                "text" => [
                    [
                        "plain_text"  => "Notion headings ",
                        "href"        => null,
                        "type"        => "text",
                        "text"        => [
                            "content" => "Notion headings ",
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

        $heading = Heading2::fromArray($array);

        $this->assertCount(2, $heading->text());
        $this->assertEquals("Notion headings rock!", $heading->toString());
        $this->assertFalse($heading->block()->archived());
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
            "heading_2"        => [
                "text"     => [],
            ],
        ];

        Heading2::fromArray($array);
    }

    public function test_transform_in_array(): void
    {
        $h = Heading2::fromString("Simple heading");

        $expected = [
            "object"           => "block",
            "created_time"     => $h->block()->createdTime()->format(DATE_ISO8601),
            "last_edited_time" => $h->block()->lastEditedType()->format(DATE_ISO8601),
            "archived"         => false,
            "has_children"      => false,
            "type"             => "heading_2",
            "heading_2"        => [
                "text" => [[
                    "plain_text"  => "Simple heading",
                    "href"        => null,
                    "type"        => "text",
                    "text"        => [
                        "content" => "Simple heading",
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
            ],
        ];

        $this->assertEquals($expected, $h->toArray());
    }

    public function test_replace_text(): void
    {
        $oldHeading = Heading2::fromString("This is an old heading");

        $newHeading = $oldHeading->withText(
            RichText::createText("This is a "),
            RichText::createText("new heading"),
        );

        $this->assertEquals("This is an old heading", $oldHeading->toString());
        $this->assertEquals("This is a new heading", $newHeading->toString());
    }

    public function test_append_text(): void
    {
        $oldHeading = Heading2::fromString("A heading");

        $newHeading = $oldHeading->appendText(
            RichText::createText(" can be extended.")
        );

        $this->assertEquals("A heading", $oldHeading->toString());
        $this->assertEquals("A heading can be extended.", $newHeading->toString());
    }
}
