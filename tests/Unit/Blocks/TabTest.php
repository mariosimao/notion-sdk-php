<?php

namespace Notion\Test\Unit\Blocks;

use Notion\Blocks\BlockFactory;
use Notion\Blocks\Divider;
use Notion\Blocks\Paragraph;
use Notion\Blocks\Tab;
use Notion\Common\Emoji;
use Notion\Exceptions\BlockException;
use Notion\Exceptions\BlockException\TabException;
use PHPUnit\Framework\TestCase;

class TabTest extends TestCase
{
    public function test_create(): void
    {
        $tab1 = Paragraph::fromString("Overview")->addChild(Paragraph::fromString("Tab 1 content"));
        $tab2 = Paragraph::fromString("Details")->addChild(Paragraph::fromString("Tab 2 content"));

        $tab = Tab::create($tab1, $tab2);

        $this->assertEquals([ $tab1, $tab2 ], $tab->tabs);
        $this->assertTrue($tab->metadata()->hasChildren);
    }

    public function test_create_empty(): void
    {
        $tab = Tab::create();

        $this->assertSame([], $tab->tabs);
        $this->assertFalse($tab->metadata()->hasChildren);
    }

    public function test_array_conversion(): void
    {
        $array = [
            "object"           => "block",
            "id"               => "04a13895-f072-4814-8af7-cd11af127040",
            "created_time"     => "2026-03-25T17:09:00.000000Z",
            "last_edited_time" => "2026-03-25T17:09:00.000000Z",
            "in_trash"         => false,
            "has_children"     => true,
            "type"             => "tab",
            "tab"              => [
                "children" => [
                    [
                        "object"           => "block",
                        "id"               => "880f4b72-28b9-497a-b9c3-dd67d61b87ef",
                        "created_time"     => "2026-03-25T17:09:00.000000Z",
                        "last_edited_time" => "2026-03-25T17:09:00.000000Z",
                        "in_trash"         => false,
                        "has_children"     => true,
                        "type"             => "paragraph",
                        "paragraph"        => [
                            "rich_text" => [],
                            "color"     => "default",
                            "icon"      => [ "type" => "emoji", "emoji" => "📋" ],
                            "children"  => [[
                                "object"           => "block",
                                "id"               => "64caffeb-c947-4acd-b6ee-b1856bb91844",
                                "created_time"     => "2026-03-25T17:09:00.000000Z",
                                "last_edited_time" => "2026-03-25T17:09:00.000000Z",
                                "in_trash"         => false,
                                "has_children"     => false,
                                "type"             => "divider",
                                "divider"          => new \stdClass(),
                            ]],
                        ],
                    ],
                ],
            ],
        ];

        $tab = Tab::fromArray($array);
        $fromFactory = BlockFactory::fromArray($array);

        $this->assertCount(1, $tab->tabs);
        $this->assertSame("📋", $tab->tabs[0]->icon?->emoji?->emoji);
        $this->assertInstanceOf(Divider::class, $tab->tabs[0]->children[0]);
        $this->assertEquals($array, $tab->toArray());
        $this->assertEquals($tab, $fromFactory);
    }

    public function test_from_api_response_without_children(): void
    {
        $array = [
            "object"           => "block",
            "id"               => "04a13895-f072-4814-8af7-cd11af127040",
            "created_time"     => "2026-03-25T17:09:00.000000Z",
            "last_edited_time" => "2026-03-25T17:09:00.000000Z",
            "in_trash"         => false,
            "has_children"     => true,
            "type"             => "tab",
            "tab"              => new \stdClass(),
        ];

        /** @var array $decoded */
        $decoded = json_decode(json_encode($array), true);
        $tab = Tab::fromArray($decoded);

        $this->assertSame([], $tab->tabs);
        $this->assertTrue($tab->metadata()->hasChildren);
    }

    public function test_from_array_with_non_paragraph_child(): void
    {
        $array = [
            "object"           => "block",
            "id"               => "04a13895-f072-4814-8af7-cd11af127040",
            "created_time"     => "2026-03-25T17:09:00.000000Z",
            "last_edited_time" => "2026-03-25T17:09:00.000000Z",
            "in_trash"         => false,
            "has_children"     => true,
            "type"             => "tab",
            "tab"              => [
                "children" => [[
                    "object"           => "block",
                    "id"               => "64caffeb-c947-4acd-b6ee-b1856bb91844",
                    "created_time"     => "2026-03-25T17:09:00.000000Z",
                    "last_edited_time" => "2026-03-25T17:09:00.000000Z",
                    "in_trash"         => false,
                    "has_children"     => false,
                    "type"             => "divider",
                    "divider"          => new \stdClass(),
                ]],
            ],
        ];

        $this->expectException(TabException::class);
        Tab::fromArray($array);
    }

    public function test_to_array_for_creation(): void
    {
        $paragraph = Paragraph::fromString("Overview")
            ->changeIcon(Emoji::fromString("📋"))
            ->addChild(Paragraph::fromString("Tab 1 content"));
        $tab = Tab::create($paragraph);

        $expected = $tab->metadata()->toArray();
        $expected["tab"] = [ "children" => [ $paragraph->toArray() ] ];

        $this->assertEquals($expected, $tab->toArray());
        $this->assertSame("tab", $expected["type"]);
    }

    public function test_add_tab(): void
    {
        $tab = Tab::create()->addTab(Paragraph::fromString("Overview"));

        $this->assertCount(1, $tab->tabs);
        $this->assertTrue($tab->metadata()->hasChildren);
    }

    public function test_change_tabs(): void
    {
        $tab = Tab::create(Paragraph::fromString("Overview"))
            ->changeTabs(Paragraph::fromString("A"), Paragraph::fromString("B"));

        $this->assertCount(2, $tab->tabs);
        $this->assertSame("B", $tab->tabs[1]->toString());

        $empty = $tab->changeTabs();
        $this->assertFalse($empty->metadata()->hasChildren);
    }

    public function test_add_child(): void
    {
        $tab = Tab::create()->addChild(Paragraph::fromString("Overview"));

        $this->assertCount(1, $tab->tabs);
    }

    public function test_add_non_paragraph_child(): void
    {
        $this->expectException(BlockException::class);
        Tab::create()->addChild(Divider::create());
    }

    public function test_change_children(): void
    {
        $tab = Tab::create()->changeChildren(
            Paragraph::fromString("A"),
            Paragraph::fromString("B"),
        );

        $this->assertCount(2, $tab->tabs);
    }

    public function test_change_children_with_non_paragraph(): void
    {
        $this->expectException(TabException::class);
        Tab::create()->changeChildren(Paragraph::fromString("A"), Divider::create());
    }

    public function test_delete(): void
    {
        $tab = Tab::create(Paragraph::fromString("A"))->delete();

        $this->assertTrue($tab->metadata()->inTrash);
    }
}
