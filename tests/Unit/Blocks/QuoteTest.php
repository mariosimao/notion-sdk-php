<?php

namespace Notion\Test\Unit\Blocks;

use Notion\Blocks\BlockFactory;
use Notion\Blocks\Exceptions\BlockTypeException;
use Notion\Blocks\Quote;
use Notion\Common\Date;
use Notion\Common\RichText;
use PHPUnit\Framework\TestCase;

class QuoteTest extends TestCase
{
    public function test_create_empty_quote(): void
    {
        $quote = Quote::create();

        $this->assertEmpty($quote->text());
        $this->assertEmpty($quote->children());
    }

    public function test_create_from_string(): void
    {
        $quote = Quote::fromString("Dummy quote.");

        $this->assertEquals("Dummy quote.", $quote->toString());
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
            "type"             => "quote",
            "quote"        => [
                "text" => [
                    [
                        "plain_text"  => "Notion quotes ",
                        "href"        => null,
                        "type"        => "text",
                        "text"        => [
                            "content" => "Notion quotes ",
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
            ],
        ];

        $quote = Quote::fromArray($array);

        $this->assertCount(2, $quote->text());
        $this->assertEmpty($quote->children());
        $this->assertEquals("Notion quotes rock!", $quote->toString());
        $this->assertFalse($quote->block()->archived());

        $this->assertEquals($quote, BlockFactory::fromArray($array));
    }

    public function test_error_on_wrong_type(): void
    {
        $this->expectException(BlockTypeException::class);
        $array = [
            "object"           => "block",
            "id"               => "04a13895-f072-4814-8af7-cd11af127040",
            "created_time"     => "2021-10-18T17:09:00.000Z",
            "last_edited_time" => "2021-10-18T17:09:00.000Z",
            "archived"         => false,
            "has_children"     => false,
            "type"             => "wrong-type",
            "quote"        => [
                "text"     => [],
                "children" => [],
            ],
        ];

        Quote::fromArray($array);
    }

    public function test_transform_in_array(): void
    {
        $q = Quote::fromString("Simple quote");

        $expected = [
            "object"           => "block",
            "created_time"     => $q->block()->createdTime()->format(Date::FORMAT),
            "last_edited_time" => $q->block()->lastEditedType()->format(Date::FORMAT),
            "archived"         => false,
            "has_children"      => false,
            "type"             => "quote",
            "quote"        => [
                "text" => [[
                    "plain_text"  => "Simple quote",
                    "href"        => null,
                    "type"        => "text",
                    "text"        => [
                        "content" => "Simple quote",
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
            ],
        ];

        $this->assertEquals($expected, $q->toArray());
    }

    public function test_replace_text(): void
    {
        $oldQuote = Quote::fromString("This is an old quote");

        $newQuote = $oldQuote->withText([
            RichText::createText("This is a "),
            RichText::createText("new quote"),
        ]);

        $this->assertEquals("This is an old quote", $oldQuote->toString());
        $this->assertEquals("This is a new quote", $newQuote->toString());
    }

    public function test_append_text(): void
    {
        $oldQuote = Quote::fromString("A quote");

        $newQuote = $oldQuote->appendText(
            RichText::createText(" can be extended.")
        );

        $this->assertEquals("A quote", $oldQuote->toString());
        $this->assertEquals("A quote can be extended.", $newQuote->toString());
    }

    public function test_replace_children(): void
    {
        $nested1 = Quote::fromString("Nested quote 1");
        $nested2 = Quote::fromString("Nested quote 2");
        $quote = Quote::fromString("Simple quote.")->withChildren([ $nested1, $nested2 ]);

        $this->assertCount(2, $quote->children());
        $this->assertEquals($nested1, $quote->children()[0]);
        $this->assertEquals($nested2, $quote->children()[1]);
    }

    public function test_append_child(): void
    {
        $quote = Quote::fromString("Simple quote.");
        $nested = Quote::fromString("Nested quote");
        $quote = $quote->appendChild($nested);

        $this->assertCount(1, $quote->children());
        $this->assertEquals($nested, $quote->children()[0]);
    }
}
