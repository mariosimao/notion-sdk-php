<?php

namespace Notion\Test\Unit\Blocks;

use Notion\Blocks\BlockFactory;
use Notion\Blocks\Callout;
use Notion\Common\Color;
use Notion\Exceptions\BlockException;
use Notion\Common\Date;
use Notion\Common\Emoji;
use Notion\Common\File;
use Notion\Common\RichText;
use PHPUnit\Framework\TestCase;

class CalloutTest extends TestCase
{
    public function test_create_empty_callout(): void
    {
        $callout = Callout::create();

        $this->assertEmpty($callout->text);
        $this->assertEmpty($callout->children);
    }

    public function test_create_from_string(): void
    {
        $callout = Callout::fromString("☀️", "Dummy callout.");

        $this->assertEquals("Dummy callout.", $callout->toString());
    }

    public function test_create_from_array_change_emoji_icon(): void
    {
        $array = [
            "object"           => "block",
            "id"               => "04a13895-f072-4814-8af7-cd11af127040",
            "created_time"     => "2021-10-18T17:09:00.000Z",
            "last_edited_time" => "2021-10-18T17:09:00.000Z",
            "archived"         => false,
            "has_children"     => false,
            "type"             => "callout",
            "callout"        => [
                "rich_text" => [
                    [
                        "plain_text"  => "Notion callouts ",
                        "href"        => null,
                        "type"        => "text",
                        "text"        => [
                            "content" => "Notion callouts ",
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
                "icon" => [
                    "type"  => "emoji",
                    "emoji" => "☀️",
                ],
                "children" => [],
            ],
        ];

        $callout = Callout::fromArray($array);

        $this->assertCount(2, $callout->text);
        $this->assertEmpty($callout->children);
        $this->assertEquals("Notion callouts rock!", $callout->toString());
        if ($callout->icon->isEmoji()) {
            $this->assertEquals("☀️", $callout->icon->emoji->toString());
        }
        $this->assertFalse($callout->metadata()->archived);

        $this->assertEquals($callout, BlockFactory::fromArray($array));
    }

    public function test_create_from_array_change_icon_file(): void
    {
        $array = [
            "object"           => "block",
            "id"               => "04a13895-f072-4814-8af7-cd11af127040",
            "created_time"     => "2021-10-18T17:09:00.000Z",
            "last_edited_time" => "2021-10-18T17:09:00.000Z",
            "archived"         => false,
            "has_children"     => false,
            "type"             => "callout",
            "callout"        => [
                "rich_text" => [
                    [
                        "plain_text"  => "Notion callouts ",
                        "href"        => null,
                        "type"        => "text",
                        "text"        => [
                            "content" => "Notion callouts ",
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
                "icon" => [
                    "type"  => "external",
                    "external"  => [
                        "type" => "external",
                        "url"  => "https://imgur.com/gallery/Iy8yE5h",
                    ],
                ],
                "children" => [],
            ],
        ];

        $callout = Callout::fromArray($array);

        $this->assertCount(2, $callout->text);
        $this->assertEmpty($callout->children);
        $this->assertEquals("Notion callouts rock!", $callout->toString());
        $this->assertFalse($callout->metadata()->archived);
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
            "callout"        => [
                "rich_text"     => [],
                "children" => [],
                "icon"     => [
                    "type"  => "emoji",
                    "emoji" => "☀️",
                ],
            ],
        ];

        Callout::fromArray($array);
    }

    public function test_error_on_wrong_icon_type(): void
    {
        $this->expectException(\ValueError::class);
        $array = [
            "object"           => "block",
            "id"               => "04a13895-f072-4814-8af7-cd11af127040",
            "created_time"     => "2021-10-18T17:09:00.000Z",
            "last_edited_time" => "2021-10-18T17:09:00.000Z",
            "archived"         => false,
            "has_children"     => false,
            "type"             => "callout",
            "callout"        => [
                "rich_text"     => [],
                "children" => [],
                "icon"     => [ "type"  => "wrong-type"],
            ],
        ];

        Callout::fromArray($array);
    }

    public function test_transform_in_array(): void
    {
        $c = Callout::fromString("☀️", "Simple callout");

        $expected = [
            "object"           => "block",
            "created_time"     => $c->metadata()->createdTime->format(Date::FORMAT),
            "last_edited_time" => $c->metadata()->lastEditedTime->format(Date::FORMAT),
            "archived"         => false,
            "has_children"      => false,
            "type"             => "callout",
            "callout"        => [
                "rich_text" => [[
                    "plain_text"  => "Simple callout",
                    "href"        => null,
                    "type"        => "text",
                    "text"        => [
                        "content" => "Simple callout",
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
                "icon" => [
                    "type"  => "emoji",
                    "emoji" => "☀️",
                ],
                "color" => "default",
                "children" => [],
            ],
        ];

        $this->assertEquals($expected, $c->toArray());
    }

    public function test_replace_text(): void
    {
        $oldCallout = Callout::fromString("☀️", "This is an old callout");

        $newCallout = $oldCallout->changeText(
            RichText::fromString("This is a "),
            RichText::fromString("new callout"),
        );

        $this->assertEquals("This is an old callout", $oldCallout->toString());
        $this->assertEquals("This is a new callout", $newCallout->toString());
    }

    public function test_add_text(): void
    {
        $oldCallout = Callout::fromString("☀️", "A callout");

        $newCallout = $oldCallout->addText(
            RichText::fromString(" can be extended.")
        );

        $this->assertEquals("A callout", $oldCallout->toString());
        $this->assertEquals("A callout can be extended.", $newCallout->toString());
    }

    public function test_replace_children(): void
    {
        $nested1 = Callout::fromString("☀️", "Nested callout 1");
        $nested2 = Callout::fromString("☀️", "Nested callout 2");
        $callout = Callout::fromString("☀️", "Simple callout.")->changeChildren($nested1, $nested2);

        $this->assertCount(2, $callout->children);
        $this->assertEquals($nested1, $callout->children[0]);
        $this->assertEquals($nested2, $callout->children[1]);
    }

    public function test_add_child(): void
    {
        $callout = Callout::fromString("☀️", "Simple callout.");
        $nested = Callout::fromString("☀️", "Nested callout");
        $callout = $callout->addChild($nested);

        $this->assertCount(1, $callout->children);
        $this->assertEquals($nested, $callout->children[0]);
    }

    public function test_replace_icon(): void
    {
        $callout = Callout::fromString("☀️", "Simple callout.")
            ->changeIcon(Emoji::fromString("🌙"));

        if ($callout->icon->isEmoji()) {
            $this->assertEquals("🌙", $callout->icon->emoji->toString());
        }
    }

    public function test_replace_icon_file(): void
    {
        $file = File::createExternal("https://example.com/icon.png");

        $callout = Callout::fromString("☀️", "Simple callout.")
            ->changeIcon($file);

        $this->assertTrue($callout->icon->isFile());
    }

    public function test_change_color(): void
    {
        $block = Callout::fromString("☀️", "Hello World!")->changeColor(Color::Red);

        $this->assertSame(Color::Red, $block->color);
    }
}
