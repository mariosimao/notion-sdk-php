<?php

namespace Notion\Test\Unit\Blocks;

use Notion\Blocks\BlockFactory;
use Notion\Blocks\Paragraph;
use Notion\Exceptions\BlockException;
use Notion\Blocks\TableOfContents;
use Notion\Common\Color;
use Notion\Common\Date;
use PHPUnit\Framework\TestCase;

class TableOfContentsTest extends TestCase
{
    public function test_create_table_of_contents(): void
    {
        $tableOfContents = TableOfContents::create();

        $this->assertEquals("table_of_contents", $tableOfContents->metadata()->type->value);
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
            "type"             => "table_of_contents",
            "table_of_contents" => [
                "color" => "green",
            ]
        ];

        $tableOfContents = TableOfContents::fromArray($array);

        $this->assertEquals($tableOfContents, BlockFactory::fromArray($array));
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
            "table_of_contents" => []
        ];

        TableOfContents::fromArray($array);
    }

    public function test_transform_in_array(): void
    {
        $tableOfContents = TableOfContents::create();

        $expected = [
            "object"           => "block",
            "created_time"     => $tableOfContents->metadata()->createdTime->format(Date::FORMAT),
            "last_edited_time" => $tableOfContents->metadata()->createdTime->format(Date::FORMAT),
            "archived"         => false,
            "has_children"     => false,
            "type"             => "table_of_contents",
            "table_of_contents" => new \stdClass(),
        ];

        $this->assertEquals($expected, $tableOfContents->toArray());
    }

    public function test_no_children_support(): void
    {
        $block = TableOfContents::create();

        $this->expectException(BlockException::class);
        /** @psalm-suppress UnusedMethodCall */
        $block->changeChildren();
    }

    public function test_no_children_support_2(): void
    {
        $block = TableOfContents::create();

        $this->expectException(BlockException::class);
        /** @psalm-suppress UnusedMethodCall */
        $block->addChild(Paragraph::create());
    }

    public function test_change_color(): void
    {
        $block = TableOfContents::create()->changeColor(Color::Red);

        $this->assertSame(Color::Red, $block->color);
    }
}
