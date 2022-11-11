<?php

namespace Notion\Test\Unit\Blocks;

use Notion\Blocks\Column;
use Notion\Exceptions\BlockException;
use Notion\Blocks\Paragraph;
use PHPUnit\Framework\TestCase;

class ColumnTest extends TestCase
{
    public function test_create_column(): void
    {
        $children = [ Paragraph::fromString("A paragraph.") ];
        $column = Column::create(...$children);

        $this->assertEquals($children, $column->children);
    }

    public function test_create_change_child_column(): void
    {
        $childColumn = Column::create(Paragraph::fromString("A paragraph"));

        $this->expectException(BlockException::class);
        Column::create($childColumn);
    }

    public function test_add_child(): void
    {
        $paragraph1 = Paragraph::fromString("Paragraph 1.");
        $paragraph2 = Paragraph::fromString("Paragraph 2.");

        $column = Column::create($paragraph1)->addChild($paragraph2);

        $this->assertEquals([ $paragraph1, $paragraph2 ], $column->children);
    }

    public function test_change_child(): void
    {
        $children1 = [ Paragraph::fromString("Paragraph 1.") ];
        $children2 = [ Paragraph::fromString("Paragraph 2.") ];

        $column = Column::create(...$children1)->changeChildren(...$children2);

        $this->assertEquals($children2, $column->children);
    }

    public function test_array_conversion(): void
    {
        $array = [
            "object"           => "block",
            "id"               => "04a13895-f072-4814-8af7-cd11af127040",
            "created_time"     => "2021-10-18T17:09:00.000000Z",
            "last_edited_time" => "2021-10-18T17:09:00.000000Z",
            "archived"         => false,
            "has_children"     => true,
            "type"             => "column",
            "column"           => [
                "children"         => [[
                    "object"           => "block",
                    "id"               => "64caffeb-c947-4acd-b6ee-b1856bb91844",
                    "created_time"     => "2021-10-18T17:09:00.000000Z",
                    "last_edited_time" => "2021-10-18T17:09:00.000000Z",
                    "archived"         => false,
                    "has_children"     => false,
                    "type"             => "divider",
                    "divider"          => new \stdClass(),
                ]],
            ],
        ];

        $column = Column::fromArray($array);

        $this->assertEquals($array, $column->toArray());
    }

    public function test_archive(): void
    {
        $block = Column::create();

        $block = $block->archive();

        $this->assertTrue($block->metadata()->archived);
    }
}
