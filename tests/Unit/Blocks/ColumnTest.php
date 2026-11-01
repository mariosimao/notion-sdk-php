<?php

namespace Notion\Test\Unit\Blocks;

use Notion\Blocks\Column;
use Notion\Blocks\Paragraph;
use Notion\Exceptions\BlockException\ColumnException;
use PHPUnit\Framework\Attributes\DataProvider;
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

        $this->expectException(ColumnException::class);
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
        ];

        $column = Column::fromArray($array);

        $this->assertEquals($array, $column->toArray());
    }

    public function test_width_ratio_is_null_by_default(): void
    {
        $column = Column::create(Paragraph::fromString("A paragraph."));

        /** @var array{column: array<string, mixed>} $array */
        $array = $column->toArray();
        $this->assertNull($column->widthRatio);
        $this->assertArrayNotHasKey("width_ratio", $array["column"]);
    }

    public function test_change_width_ratio(): void
    {
        $column = Column::create(Paragraph::fromString("A paragraph."))->changeWidthRatio(0.25);

        /** @var array{column: array{width_ratio: float}} $array */
        $array = $column->toArray();
        $this->assertSame(0.25, $column->widthRatio);
        $this->assertSame(0.25, $array["column"]["width_ratio"]);
    }

    public function test_remove_width_ratio(): void
    {
        $column = Column::create()->changeWidthRatio(0.25)->removeWidthRatio();

        $this->assertNull($column->widthRatio);
    }

    public function test_width_ratio_is_preserved(): void
    {
        $column = Column::create()
            ->changeWidthRatio(0.5)
            ->addChild(Paragraph::fromString("1"))
            ->changeChildren(Paragraph::fromString("2"))
            ->delete();

        $this->assertInstanceOf(Column::class, $column);
        $this->assertSame(0.5, $column->widthRatio);
    }

    public function test_width_ratio_of_one_is_allowed(): void
    {
        $this->assertSame(1.0, Column::create()->changeWidthRatio(1)->widthRatio);
    }

    /** @return array<string, array{float}> */
    public static function invalidWidthRatioProvider(): array
    {
        return [
            "zero"             => [0.0],
            "negative"         => [-0.5],
            "greater than one" => [1.5],
        ];
    }

    #[DataProvider("invalidWidthRatioProvider")]
    public function test_invalid_width_ratio(float $widthRatio): void
    {
        $this->expectException(ColumnException::class);
        Column::create()->changeWidthRatio($widthRatio);
    }

    public function test_width_ratio_array_conversion(): void
    {
        $array = [
            "object"           => "block",
            "id"               => "04a13895-f072-4814-8af7-cd11af127040",
            "created_time"     => "2021-10-18T17:09:00.000000Z",
            "last_edited_time" => "2021-10-18T17:09:00.000000Z",
            "in_trash"         => false,
            "has_children"     => true,
            "type"             => "column",
            "column"           => [
                "children"    => [],
                "width_ratio" => 0.75,
            ],
        ];

        $column = Column::fromArray($array);

        $this->assertSame(0.75, $column->widthRatio);
        $this->assertEquals($array, $column->toArray());
    }

    public function test_integer_width_ratio_from_array(): void
    {
        $column = Column::fromArray([
            "object"           => "block",
            "id"               => "04a13895-f072-4814-8af7-cd11af127040",
            "created_time"     => "2021-10-18T17:09:00.000000Z",
            "last_edited_time" => "2021-10-18T17:09:00.000000Z",
            "in_trash"         => false,
            "has_children"     => true,
            "type"             => "column",
            "column"           => [ "width_ratio" => 1 ],
        ]);

        $this->assertSame(1.0, $column->widthRatio);
    }

    public function test_delete(): void
    {
        $block = Column::create();

        $block = $block->delete();

        $this->assertTrue($block->metadata()->inTrash);
    }
}
