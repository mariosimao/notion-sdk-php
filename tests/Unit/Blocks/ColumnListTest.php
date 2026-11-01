<?php

namespace Notion\Test\Unit\Blocks;

use Notion\Blocks\BlockFactory;
use Notion\Blocks\Column;
use Notion\Blocks\ColumnList;
use Notion\Exceptions\BlockException;
use Notion\Blocks\Paragraph;
use Notion\Exceptions\BlockException\ColumnListException;
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
            "in_trash"         => false,
            "has_children"     => true,
            "type"             => "column_list",
            "column_list"      => [
                "children"     => [
                    [
                        "object"           => "block",
                        "id"               => "880f4b72-28b9-497a-b9c3-dd67d61b87ef",
                        "created_time"     => "2021-10-18T17:09:00.000000Z",
                        "last_edited_time" => "2021-10-18T17:09:00.000000Z",
                        "in_trash"         => false,
                        "has_children"     => true,
                        "type"             => "column",
                        "column"           => [
                            "children"         => [[
                                "object"           => "block",
                                "id"               => "64caffeb-c947-4acd-b6ee-b1856bb91844",
                                "created_time"     => "2021-10-18T17:09:00.000000Z",
                                "last_edited_time" => "2021-10-18T17:09:00.000000Z",
                                "in_trash"         => false,
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
                        "in_trash"         => false,
                        "has_children"     => true,
                        "type"             => "column",
                        "column"           => [
                            "children"         => [[
                                "object"           => "block",
                                "id"               => "e99edc10-621a-43cf-9a99-eca8d10ded44",
                                "created_time"     => "2021-10-18T17:09:00.000000Z",
                                "last_edited_time" => "2021-10-18T17:09:00.000000Z",
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

    public function test_delete(): void
    {
        $block = ColumnList::create();

        $block = $block->delete();

        $this->assertTrue($block->metadata()->inTrash);
    }

    public function test_create_with_width_ratios(): void
    {
        $column1 = Column::create(Paragraph::fromString("1"))->changeWidthRatio(0.25);
        $column2 = Column::create(Paragraph::fromString("2"))->changeWidthRatio(0.75);

        $list = ColumnList::create($column1, $column2);

        /** @var array{column_list: array{children: list<array{column: array{width_ratio: float}}>}} $array */
        $array = $list->toArray();
        $children = $array["column_list"]["children"];
        $this->assertSame(0.25, $children[0]["column"]["width_ratio"]);
        $this->assertSame(0.75, $children[1]["column"]["width_ratio"]);
    }

    public function test_create_with_width_ratios_tolerates_float_imprecision(): void
    {
        $list = ColumnList::create(
            Column::create()->changeWidthRatio(0.1),
            Column::create()->changeWidthRatio(0.2),
            Column::create()->changeWidthRatio(0.7),
        );

        $this->assertCount(3, $list->columns);
    }

    public function test_create_with_partial_width_ratios(): void
    {
        $list = ColumnList::create(
            Column::create()->changeWidthRatio(0.5),
            Column::create(),
            Column::create(),
        );

        $this->assertCount(3, $list->columns);
    }

    public function test_create_with_width_ratios_not_adding_up_to_one(): void
    {
        $this->expectException(ColumnListException::class);
        $this->expectExceptionMessage("must add up to 1");

        ColumnList::create(
            Column::create()->changeWidthRatio(0.25),
            Column::create()->changeWidthRatio(0.5),
        );
    }

    public function test_create_with_width_ratios_exceeding_one(): void
    {
        $this->expectException(ColumnListException::class);
        $this->expectExceptionMessage("must not add up to more than 1");

        ColumnList::create(
            Column::create()->changeWidthRatio(0.75),
            Column::create()->changeWidthRatio(0.5),
            Column::create(),
        );
    }

    public function test_add_child_with_width_ratios(): void
    {
        $list = ColumnList::create()
            ->addChild(Column::create()->changeWidthRatio(0.25))
            ->addChild(Column::create()->changeWidthRatio(0.75));

        $this->assertCount(2, $list->columns);
    }

    public function test_add_child_with_width_ratios_exceeding_one(): void
    {
        $list = ColumnList::create()->addChild(Column::create()->changeWidthRatio(0.75));

        $this->expectException(ColumnListException::class);
        $list->addChild(Column::create()->changeWidthRatio(0.5));
    }

    public function test_change_children_with_width_ratios_not_adding_up_to_one(): void
    {
        $this->expectException(ColumnListException::class);

        ColumnList::create()->changeChildren(
            Column::create()->changeWidthRatio(0.25),
            Column::create()->changeWidthRatio(0.25),
        );
    }

    public function test_from_array_does_not_validate_width_ratios(): void
    {
        $column = [
            "object"           => "block",
            "id"               => "880f4b72-28b9-497a-b9c3-dd67d61b87ef",
            "created_time"     => "2021-10-18T17:09:00.000000Z",
            "last_edited_time" => "2021-10-18T17:09:00.000000Z",
            "in_trash"         => false,
            "has_children"     => false,
            "type"             => "column",
            "column"           => [ "children" => [], "width_ratio" => 0.25 ],
        ];

        $list = ColumnList::fromArray([
            "object"           => "block",
            "id"               => "04a13895-f072-4814-8af7-cd11af127040",
            "created_time"     => "2021-10-18T17:09:00.000000Z",
            "last_edited_time" => "2021-10-18T17:09:00.000000Z",
            "in_trash"         => false,
            "has_children"     => true,
            "type"             => "column_list",
            "column_list"      => [ "children" => [ $column, $column ] ],
        ]);

        $this->assertSame(0.25, $list->columns[0]->widthRatio);
    }
}
