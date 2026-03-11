<?php

namespace Notion\Test\Unit\Blocks;

use Notion\Blocks\BlockFactory;
use Notion\Blocks\Column;
use Notion\Blocks\ColumnList;
use Notion\Exceptions\BlockException;
use Notion\Blocks\Paragraph;
use Notion\Exceptions\ColumnListException;
use PHPUnit\Framework\TestCase;

class ColumnListTest extends TestCase
{
    public function test_create(): void
    {
        $column1 = Column::create(Paragraph::fromString("Paragraph 1"));
        $column2 = Column::create(Paragraph::fromString("Paragraph 2"));

        $list = ColumnList::create($column1, $column2);

        $this->assertEquals([ $column1, $column2 ], $list->columns);
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
            "type"             => "column_list",
            "column_list"      => [
                "children"     => [
                    [
                        "object"           => "block",
                        "id"               => "880f4b72-28b9-497a-b9c3-dd67d61b87ef",
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
                    ],
                    [
                        "object"           => "block",
                        "id"               => "c45d7fef-08ff-4638-843d-d984c7d3ef72",
                        "created_time"     => "2021-10-18T17:09:00.000000Z",
                        "last_edited_time" => "2021-10-18T17:09:00.000000Z",
                        "archived"         => false,
                        "has_children"     => true,
                        "type"             => "column",
                        "column"           => [
                            "children"         => [[
                                "object"           => "block",
                                "id"               => "e99edc10-621a-43cf-9a99-eca8d10ded44",
                                "created_time"     => "2021-10-18T17:09:00.000000Z",
                                "last_edited_time" => "2021-10-18T17:09:00.000000Z",
                                "archived"         => false,
                                "has_children"     => false,
                                "type"             => "divider",
                                "divider"          => new \stdClass(),
                            ]],
                        ],
                    ],
                ],
            ],
        ];

        $list = ColumnList::fromArray($array);
        $listFromFactory = BlockFactory::fromArray($array);

        $this->assertEquals($list, $listFromFactory);
        $this->assertEquals($array, $list->toArray());
        $this->assertEquals("04a13895-f072-4814-8af7-cd11af127040", $list->metadata()->id);
        $this->assertTrue($list->metadata()->hasChildren);
    }

    public function test_change_children(): void
    {
        $column1 = Column::create(Paragraph::fromString("Paragraph 1"));
        $column2 = Column::create(Paragraph::fromString("Paragraph 2"));

        $list = ColumnList::create($column1)->changeChildren($column2);
        $this->assertEquals([ $column2 ], $list->columns);
    }

    public function test_change_children_to_not_columns(): void
    {
        $column = Column::create(Paragraph::fromString("Paragraph 1"));

        $list = ColumnList::create($column);

        $this->expectException(BlockException::class);
        /** @psalm-suppress UnusedMethodCall */
        $list->changeChildren(Paragraph::fromString("This should be a column."));
    }

    public function test_add_child(): void
    {
        $column = Column::create(Paragraph::fromString("Paragraph 1"));

        $list = ColumnList::create()->addChild($column);
        $this->assertEquals([ $column ], $list->columns);
    }

    public function test_add_child_that_is_not_column(): void
    {
        $child = Paragraph::fromString("Not a column");

        $this->expectException(ColumnListException::class);
        /** @psalm-suppress UnusedMethodCall */
        ColumnList::create()->addChild($child);
    }

    public function test_archive(): void
    {
        $block = ColumnList::create();

        $block = $block->archive();

        $this->assertTrue($block->metadata()->archived);
    }
}
