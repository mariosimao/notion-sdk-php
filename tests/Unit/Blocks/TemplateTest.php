<?php

namespace Notion\Test\Unit\Blocks;

use Notion\Blocks\BlockFactory;
use Notion\Blocks\BlockType;
use Notion\Blocks\Paragraph;
use Notion\Blocks\Template;
use Notion\Common\Date;
use Notion\Common\RichText;
use Notion\Exceptions\BlockException;
use PHPUnit\Framework\TestCase;

class TemplateTest extends TestCase
{
    public function test_create(): void
    {
        $template = Template::create();

        $this->assertSame(BlockType::Template, $template->metadata()->type);
        $this->assertEmpty($template->text);
        $this->assertEmpty($template->children);
        $this->assertFalse($template->metadata()->hasChildren);
    }

    public function test_create_with_text(): void
    {
        $template = Template::create(RichText::fromString("Add a new task"));

        $this->assertSame(BlockType::Template, $template->metadata()->type);
        $this->assertCount(1, $template->text);
        $this->assertSame("Add a new task", $template->toString());
        $this->assertEmpty($template->children);
    }

    public function test_from_array(): void
    {
        $array = [
            "object"           => "block",
            "id"               => "04a13895-f072-4814-8af7-cd11af127040",
            "created_time"     => "2021-10-18T17:09:00.000Z",
            "last_edited_time" => "2021-10-18T17:09:00.000Z",
            "in_trash"         => false,
            "has_children"     => false,
            "type"             => "template",
            "template"         => [
                "rich_text" => [
                    [
                        "plain_text"  => "Add a new to-do",
                        "href"        => null,
                        "type"        => "text",
                        "text"        => [
                            "content" => "Add a new to-do",
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
            ],
        ];

        $template = Template::fromArray($array);

        $this->assertSame(BlockType::Template, $template->metadata()->type);
        $this->assertCount(1, $template->text);
        $this->assertSame("Add a new to-do", $template->toString());
        $this->assertEmpty($template->children);
        $this->assertFalse($template->metadata()->inTrash);

        $this->assertEquals($template, BlockFactory::fromArray($array));
    }

    public function test_from_array_with_children(): void
    {
        $array = [
            "object"           => "block",
            "id"               => "04a13895-f072-4814-8af7-cd11af127040",
            "created_time"     => "2021-10-18T17:09:00.000Z",
            "last_edited_time" => "2021-10-18T17:09:00.000Z",
            "in_trash"         => false,
            "has_children"     => true,
            "type"             => "template",
            "template"         => [
                "rich_text" => [],
                "children"  => [
                    [
                        "object"           => "block",
                        "id"               => "c31671e1-e120-4e31-89e8-466d3a86c671",
                        "created_time"     => "2021-10-18T17:09:00.000Z",
                        "last_edited_time" => "2021-10-18T17:09:00.000Z",
                        "in_trash"         => false,
                        "has_children"     => false,
                        "type"             => "paragraph",
                        "paragraph"        => [
                            "rich_text" => [],
                            "color"     => "default",
                        ],
                    ],
                ],
            ],
        ];

        $template = Template::fromArray($array);

        $this->assertCount(1, $template->children);
        $this->assertInstanceOf(Paragraph::class, $template->children[0]);
    }

    public function test_error_on_wrong_type(): void
    {
        $this->expectException(BlockException::class);
        $array = [
            "object"           => "block",
            "id"               => "04a13895-f072-4814-8af7-cd11af127040",
            "created_time"     => "2021-10-18T17:09:00.000Z",
            "last_edited_time" => "2021-10-18T17:09:00.000Z",
            "in_trash"         => false,
            "has_children"     => false,
            "type"             => "wrong-type",
            "template"         => [
                "rich_text" => [],
                "children"  => [],
            ],
        ];

        Template::fromArray($array);
    }

    public function test_to_array(): void
    {
        $template = Template::create(RichText::fromString("Template title"));

        $expected = [
            "object"           => "block",
            "created_time"     => $template->metadata()->createdTime->format(Date::FORMAT),
            "last_edited_time" => $template->metadata()->lastEditedTime->format(Date::FORMAT),
            "in_trash"         => false,
            "has_children"     => false,
            "type"             => "template",
            "template"         => [
                "rich_text" => [
                    [
                        "plain_text"  => "Template title",
                        "href"        => null,
                        "type"        => "text",
                        "text"        => [
                            "content" => "Template title",
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
                "children" => [],
            ],
        ];

        $this->assertEquals($expected, $template->toArray());
    }

    public function test_to_array_with_children(): void
    {
        $child = Paragraph::fromString("Child paragraph");
        $template = Template::create(RichText::fromString("Button"))
            ->addChild($child);

        $array = $template->toArray();

        $this->assertTrue($template->metadata()->hasChildren);
        /** @var array{ rich_text: list<mixed>, children: list<array{ type: string, ... }> } $templateData */
        $templateData = $array["template"];
        $this->assertCount(1, $templateData["children"]);
        $this->assertSame("paragraph", $templateData["children"][0]["type"]);
    }

    public function test_change_text(): void
    {
        $old = Template::create(RichText::fromString("Old label"));
        $new = $old->changeText(
            RichText::fromString("New "),
            RichText::fromString("label"),
        );

        $this->assertSame("Old label", $old->toString());
        $this->assertSame("New label", $new->toString());
    }

    public function test_add_text(): void
    {
        $old = Template::create(RichText::fromString("Add a new"));
        $new = $old->addText(RichText::fromString(" item"));

        $this->assertSame("Add a new", $old->toString());
        $this->assertSame("Add a new item", $new->toString());
    }

    public function test_change_children(): void
    {
        $child1 = Paragraph::fromString("Child 1");
        $child2 = Paragraph::fromString("Child 2");

        $template = Template::create()->changeChildren($child1, $child2);

        $this->assertTrue($template->metadata()->hasChildren);
        $this->assertCount(2, $template->children);
        $this->assertSame($child1, $template->children[0]);
        $this->assertSame($child2, $template->children[1]);

        $emptyTemplate = $template->changeChildren();
        $this->assertFalse($emptyTemplate->metadata()->hasChildren);
        $this->assertEmpty($emptyTemplate->children);
    }

    public function test_add_child(): void
    {
        $child = Paragraph::fromString("Child block");
        $template = Template::create()->addChild($child);

        $this->assertTrue($template->metadata()->hasChildren);
        $this->assertCount(1, $template->children);
        $this->assertSame($child, $template->children[0]);
    }

    public function test_delete(): void
    {
        $template = Template::create();
        $deleted = $template->delete();

        $this->assertFalse($template->metadata()->inTrash);
        $this->assertTrue($deleted->metadata()->inTrash);
    }
}
