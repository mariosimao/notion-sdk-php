<?php

namespace Notion\Test\Unit\Common;

use Notion\Common\Text;
use PHPUnit\Framework\TestCase;

class TextTest extends TestCase
{
    public function test_create_text(): void
    {
        $text = Text::create("Simple text");

        $this->assertEquals("Simple text", $text->content());
    }

    public function test_change_text(): void
    {
        $text = Text::create("")->withContent("Simple text");

        $this->assertEquals("Simple text", $text->content());
    }

    public function test_change_url(): void
    {
        $text = Text::create("Simple text")->withUrl("https://notion.so");

        $this->assertEquals("https://notion.so", $text->url());
    }

    public function test_remove_url(): void
    {
        $text = Text::create("Simple text")
            ->withUrl("https://notion.so")
            ->withoutUrl();

        $this->assertNull($text->url());
    }
}
