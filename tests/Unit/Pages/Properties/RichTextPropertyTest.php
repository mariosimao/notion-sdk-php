<?php

namespace Notion\Test\Unit\Pages\Properties;

use Notion\Common\RichText;
use Notion\Pages\Properties\PropertyFactory;
use Notion\Pages\Properties\PropertyType;
use Notion\Pages\Properties\RichTextProperty;
use PHPUnit\Framework\TestCase;

class RichTextPropertyTest extends TestCase
{
    public function test_create(): void
    {
        $text = RichTextProperty::fromText(RichText::fromString("Dummy text"));

        $this->assertEquals("Dummy text", $text->text[0]->text?->content);
        $this->assertEquals("", $text->metadata()->id);
        $this->assertTrue($text->metadata()->type === PropertyType::RichText);
    }

    public function test_create_empty(): void
    {
        $text = RichTextProperty::createEmpty();

        $this->assertTrue($text->isEmpty());
    }

    public function test_array_conversion(): void
    {
        $array = [
            "id"    => "a7ede3b7-c7ae-4eb8-b415-a7f80ac4dfe5",
            "type"  => "rich_text",
            "rich_text" => [[
                "plain_text" => "Dummy text",
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
                    "content" => "Dummy text",
                ],
            ]],
        ];

        $text = RichTextProperty::fromArray($array);
        $fromFactory = PropertyFactory::fromArray($array);

        $this->assertEquals($array, $text->toArray());
        $this->assertEquals($array, $fromFactory->toArray());
    }

    public function test_string_conversion(): void
    {
        $text = RichTextProperty::fromText(RichText::fromString("Dummy text"));
        $this->assertEquals("Dummy text", $text->toString());
    }

    public function test_change_text(): void
    {
        $text = RichTextProperty::fromText()->changeText(
            RichText::fromString("Dummy text")
        );
        $this->assertEquals("Dummy text", $text->toString());
    }

    public function test_clear(): void
    {
        $text = RichTextProperty::fromString("Dummy text")->clear();

        $this->assertTrue($text->isEmpty());
    }
}
