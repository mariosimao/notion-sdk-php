<?php

namespace Notion\Test\Unit\Blocks;

use Notion\Blocks\BlockFactory;
use Notion\Blocks\Code;
use Notion\Blocks\Exceptions\BlockTypeException;
use Notion\Common\Date;
use Notion\Common\RichText;
use Notion\NotionException;
use PHPUnit\Framework\TestCase;

class CodeTest extends TestCase
{
    public function test_create_empty_code_block(): void
    {
        $code = Code::create();

        $this->assertEmpty($code->text());
        $this->assertEquals(Code::LANG_PLAIN_TEXT, $code->language());
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
            "type"             => "code",
            "code"        => [
                "text" => [
                    [
                        "plain_text"  => "<?php\necho 'Hello World!';",
                        "href"        => null,
                        "type"        => "text",
                        "text"        => [
                            "content" => "<?php\necho 'Hello World!';",
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
                ],
                "language" => "php",
            ],
        ];

        $code = Code::fromArray($array);

        $this->assertCount(1, $code->text());
        $this->assertEquals("<?php\necho 'Hello World!';", $code->toString());
        $this->assertEquals($code, BlockFactory::fromArray($array));
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
            "code"             => [
                "language" => "php",
                "text" => [],
            ],
        ];

        $this->expectException(\Exception::class);
        Code::fromArray($array);
    }

    public function test_transform_in_array(): void
    {
        $h = Code::create("<?php\necho 'Hello World!';", "php");

        $expected = [
            "object"           => "block",
            "created_time"     => $h->block()->createdTime()->format(Date::FORMAT),
            "last_edited_time" => $h->block()->lastEditedType()->format(Date::FORMAT),
            "archived"         => false,
            "has_children"      => false,
            "type"             => "code",
            "code"        => [
                "language" => "php",
                "text" => [[
                    "plain_text"  => "<?php\necho 'Hello World!';",
                    "href"        => null,
                    "type"        => "text",
                    "text"        => [
                        "content" => "<?php\necho 'Hello World!';",
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
        $oldHeading = Code::create("This is an old code");

        $newHeading = $oldHeading->withText([
            RichText::createText("This is a "),
            RichText::createText("new code"),
        ]);

        $this->assertEquals("This is an old code", $oldHeading->toString());
        $this->assertEquals("This is a new code", $newHeading->toString());
    }

    public function test_append_text(): void
    {
        $oldHeading = Code::create("A code");

        $newHeading = $oldHeading->appendText(
            RichText::createText(" can be extended.")
        );

        $this->assertEquals("A code", $oldHeading->toString());
        $this->assertEquals("A code can be extended.", $newHeading->toString());
    }

    public function test_change_language(): void
    {
        $code = Code::create("Simple code")->withLanguage("php");

        $this->assertEquals("php", $code->language());
    }

    public function test_no_children_support(): void
    {
        $block = Code::create();

        $this->expectException(NotionException::class);
        /** @psalm-suppress UnusedMethodCall */
        $block->changeChildren([]);
    }

    public function test_array_for_update_operations(): void
    {
        $block = Code::create();

        $array = $block->toUpdateArray();

        $this->assertCount(2, $array);
    }
}
