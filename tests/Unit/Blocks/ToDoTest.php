<?php

namespace Notion\Test\Unit\Blocks;

use Notion\Blocks\BlockFactory;
use Notion\Exceptions\BlockException;
use Notion\Blocks\ToDo;
use Notion\Common\Color;
use Notion\Common\Date;
use Notion\Common\RichText;
use PHPUnit\Framework\TestCase;

class ToDoTest extends TestCase
{
    public function test_create_empty_to_do(): void
    {
        $toDo = ToDo::create();

        $this->assertEmpty($toDo->text);
        $this->assertEmpty($toDo->children);
        $this->assertFalse($toDo->checked);
    }

    public function test_create_from_string(): void
    {
        $toDo = ToDo::fromString("Dummy to do.");

        $this->assertEquals("Dummy to do.", $toDo->toString());
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
            "type"             => "to_do",
            "to_do"        => [
                "checked" => true,
                "rich_text" => [
                    [
                        "plain_text"  => "Notion to dos ",
                        "href"        => null,
                        "type"        => "text",
                        "text"        => [
                            "content" => "Notion to dos ",
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

        $toDo = ToDo::fromArray($array);

        $this->assertCount(2, $toDo->text);
        $this->assertEmpty($toDo->children);
        $this->assertEquals("Notion to dos rock!", $toDo->toString());
        $this->assertTrue($toDo->checked);
        $this->assertFalse($toDo->metadata()->archived);

        $this->assertEquals($toDo, BlockFactory::fromArray($array));
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
            "to_do"        => [
                "checked"  => false,
                "rich_text"     => [],
                "children" => [],
            ],
        ];

        ToDo::fromArray($array);
    }

    public function test_transform_in_array(): void
    {
        $p = ToDo::fromString("Simple to do");

        $expected = [
            "object"           => "block",
            "created_time"     => $p->metadata()->createdTime->format(Date::FORMAT),
            "last_edited_time" => $p->metadata()->lastEditedTime->format(Date::FORMAT),
            "archived"         => false,
            "has_children"     => false,
            "type"             => "to_do",
            "to_do"            => [
                "checked" => false,
                "rich_text" => [[
                    "plain_text"  => "Simple to do",
                    "href"        => null,
                    "type"        => "text",
                    "text"        => [
                        "content" => "Simple to do",
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
                "color" => "default",
                "children" => [],
            ],
        ];

        $this->assertEquals($expected, $p->toArray());
    }

    public function test_replace_text(): void
    {
        $oldToDo = ToDo::fromString("This is an old to do");

        $newToDo = $oldToDo->changeText(
            RichText::fromString("This is a "),
            RichText::fromString("new to do"),
        );

        $this->assertEquals("This is an old to do", $oldToDo->toString());
        $this->assertEquals("This is a new to do", $newToDo->toString());
    }

    public function test_add_text(): void
    {
        $oldToDo = ToDo::fromString("A to do");

        $newToDo = $oldToDo->addText(
            RichText::fromString(" can be extended.")
        );

        $this->assertEquals("A to do", $oldToDo->toString());
        $this->assertEquals("A to do can be extended.", $newToDo->toString());
    }

    public function test_replace_children(): void
    {
        $nested1 = ToDo::fromString("Nested to do 1");
        $nested2 = ToDo::fromString("Nested to do 2");
        $toDo = ToDo::fromString("Simple to do.")->changeChildren($nested1, $nested2);

        $this->assertCount(2, $toDo->children);
        $this->assertEquals($nested1, $toDo->children[0]);
        $this->assertEquals($nested2, $toDo->children[1]);
    }

    public function test_add_child(): void
    {
        $toDo = ToDo::fromString("Simple to do.");
        $nested = ToDo::fromString("Nested to do");
        $toDo = $toDo->addChild($nested);

        $this->assertCount(1, $toDo->children);
        $this->assertEquals($nested, $toDo->children[0]);
    }

    public function test_check_item(): void
    {
        $toDo = ToDo::fromString("Simple to do.");
        $toDo = $toDo->check();

        $this->assertTrue($toDo->checked);
    }

    public function test_uncheck_item(): void
    {
        $toDo = ToDo::fromString("Simple to do.")->check()->uncheck();

        $this->assertFalse($toDo->checked);
    }

    public function test_change_color(): void
    {
        $block = ToDo::fromString("Hello World!")->changeColor(Color::Red);

        $this->assertSame(Color::Red, $block->color);
    }
}
