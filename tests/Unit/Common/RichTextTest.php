<?php

namespace Notion\Test\Unit\Common;

use Notion\Common\Color;
use Notion\Common\Equation;
use Notion\Common\RichText;
use Notion\Common\Text;
use PHPUnit\Framework\TestCase;

class RichTextTest extends TestCase
{
    public function test_create_text(): void
    {
        $richText = RichText::createText("Simple text");

        $this->assertTrue($richText->isText());
        $this->assertEquals("Simple text", $richText->text?->content);
    }

    public function test_create_link(): void
    {
        $richText = RichText::createLink("Click here", "https://notion.so");

        $this->assertTrue($richText->isText());
        $this->assertEquals("Click here", $richText->plainText);
        $this->assertEquals("Click here", $richText->text?->content);
        $this->assertEquals("https://notion.so", $richText->href);
        $this->assertEquals("https://notion.so", $richText->text?->url);
    }

    public function test_create_from_text(): void
    {
        $text = Text::create("My text");

        $richText = RichText::createFromText($text);

        $this->assertTrue($richText->isText());
        $this->assertEquals("My text", $richText->plainText);
        $this->assertEquals("My text", $richText->text?->content);
    }

    public function test_create_equation(): void
    {
        $richText = RichText::createEquation("a^2 + b^2 = c^2");

        $this->assertTrue($richText->isEquation());
        $this->assertEquals("a^2 + b^2 = c^2", $richText->equation?->expression);
    }

    public function test_create_from_equation(): void
    {
        $equation = Equation::create("a^2 + b^2 = c^2");
        $richText = RichText::createFromEquation($equation);

        $this->assertTrue($richText->isEquation());
        $this->assertEquals("a^2 + b^2 = c^2", $richText->equation?->expression);
    }

    public function test_change_to_bold(): void
    {
        $richText = RichText::createText("Simple text")->bold();

        $this->assertTrue($richText->annotations->isBold);
    }

    public function test_change_to_italic(): void
    {
        $richText = RichText::createText("Simple text")->italic();

        $this->assertTrue($richText->annotations->isItalic);
    }

    public function test_change_to_strike_through(): void
    {
        $richText = RichText::createText("Simple text")->strikeThrough();

        $this->assertTrue($richText->annotations->isStrikeThrough);
    }

    public function test_change_to_underline(): void
    {
        $richText = RichText::createText("Simple text")->underline();

        $this->assertTrue($richText->annotations->isUnderline);
    }

    public function test_change_to_code(): void
    {
        $richText = RichText::createText("Simple text")->code();

        $this->assertTrue($richText->annotations->isCode);
    }

    public function test_change_color(): void
    {
        $richText = RichText::createText("Simple text")->color(Color::Red);

        $this->assertEquals(Color::Red, $richText->annotations->color);
    }

    public function test_change_href(): void
    {
        $richText = RichText::createText("Simple text")->changeHref("https://notion.so");

        $this->assertEquals("https://notion.so", $richText->href);
    }

    public function test_mention_array_conversion(): void
    {
        $array = [
            "plain_text" => "Page title",
            "href" => null,
            "annotations" => [
                "bold"          => false,
                "italic"        => false,
                "strikethrough" => false,
                "underline"     => false,
                "code"          => false,
                "color"         => "default",
            ],
            "type" => "mention",
            "mention" => [
                "type" => "page",
                "page" => [ "id" => "1ce62b6f-b7f3-4201-afd0-08acb02e61c6" ],
            ],
        ];
        $richText = RichText::fromArray($array);

        $this->assertEquals($array, $richText->toArray());
        $this->assertNotNull($richText->mention);
    }

    public function test_equation_array_conversion(): void
    {
        $array = [
            "plain_text" => "Page title",
            "href" => null,
            "annotations" => [
                "bold"          => false,
                "italic"        => false,
                "strikethrough" => false,
                "underline"     => false,
                "code"          => false,
                "color"         => "default",
            ],
            "type" => "equation",
            "equation" => [ "expression" => "a^2 + b^2 = c^2" ],
        ];
        $richText = RichText::fromArray($array);

        $this->assertEquals($array, $richText->toArray());
    }
}
