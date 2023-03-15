<?php

namespace Notion\Test\Unit\Pages\Properties;

use Notion\Common\RichText;
use Notion\Pages\Properties\PropertyType;
use Notion\Pages\Properties\Title;
use PHPUnit\Framework\TestCase;

class TitleTest extends TestCase
{
    public function test_create(): void
    {
        $title = Title::fromText(RichText::fromString("Dummy title"));

        $this->assertEquals("Dummy title", $title->title[0]->text?->content);
        $this->assertEquals("title", $title->metadata()->id);
        $this->assertTrue($title->metadata()->type === PropertyType::Title);
    }

    public function test_array_conversion(): void
    {
        $array = [
            "id"    => "title",
            "type"  => "title",
            "title" => [[
                "plain_text" => "Dummy title",
                "href" => null,
                "annotations" => [
                    "bold"          => false,
                    "italic"        => false,
                    "strikethrough" => false,
                    "underline"     => false,
                    "code"          => false,
                    "color"         => "default",
                ],
                "type" => "text",
                "text" => [
                    "content" => "Dummy title",
                ],
            ]],
        ];

        $title = Title::fromArray($array);
        $this->assertEquals($array, $title->toArray());
    }

    public function test_string_conversion(): void
    {
        $title = Title::fromText(RichText::fromString("Dummy title"));
        $this->assertEquals("Dummy title", $title->toString());
    }

    public function test_change_text(): void
    {
        $title = Title::fromText()->change(
            RichText::fromString("Dummy title")
        );
        $this->assertEquals("Dummy title", $title->toString());
    }

    public function test_is_empty_on_empty_string(): void
    {
        $this->assertTrue(Title::fromString("")->isEmpty());
    }

    public function test_is_empty_on_no_rich_text(): void
    {
        $this->assertTrue(Title::fromText()->isEmpty());
    }
}
