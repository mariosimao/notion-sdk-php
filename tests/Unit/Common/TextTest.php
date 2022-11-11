<?php

namespace Notion\Test\Unit\Common;

use Notion\Common\Text;
use PHPUnit\Framework\TestCase;

class TextTest extends TestCase
{
    public function test_create_text(): void
    {
        $text = Text::fromString("Simple text");

        $this->assertEquals("Simple text", $text->content);
    }

    public function test_change_text(): void
    {
        $text = Text::fromString("")->changeContent("Simple text");

        $this->assertEquals("Simple text", $text->content);
    }

    public function test_change_url(): void
    {
        $text = Text::fromString("Simple text")->changeUrl("https://notion.so");

        $this->assertEquals("https://notion.so", $text->url);
    }

    public function test_remove_url(): void
    {
        $text = Text::fromString("Simple text")
            ->changeUrl("https://notion.so")
            ->removeUrl();

        $this->assertNull($text->url);
    }

    public function test_convert_test_with_url_to_array(): void
    {
        $text = Text::fromString("Simple text")
            ->changeUrl("https://notion.so");

        $expected = [
            "content" => "Simple text",
            "link"    => [ "url" => "https://notion.so" ],
        ];

        $this->assertSame($expected, $text->toArray());
    }
}
