<?php

namespace Notion\Test\Unit\Blocks;

use Notion\Blocks\BlockFactory;
use Notion\Blocks\Exceptions\BlockTypeException;
use Notion\Blocks\TableOfContents;
use Notion\Common\Date;
use Notion\NotionException;
use PHPUnit\Framework\TestCase;

class TableOfContentsTest extends TestCase
{
    public function test_create_table_of_contents(): void
    {
        $tableOfContents = TableOfContents::create();

        $this->assertEquals("table_of_contents", $tableOfContents->block()->type());
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
            "table_of_contents" => [],
        ];

        $tableOfContents = TableOfContents::fromArray($array);

        $this->assertTrue($tableOfContents->block()->isTableOfContents());

        $this->assertEquals($tableOfContents, BlockFactory::fromArray($array));
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
            "table_of_contents" => [],
        ];

        TableOfContents::fromArray($array);
    }

    public function test_transform_in_array(): void
    {
        $tableOfContents = TableOfContents::create();

        $expected = [
            "object"           => "block",
            "created_time"     => $tableOfContents->block()->createdTime()->format(Date::FORMAT),
            "last_edited_time" => $tableOfContents->block()->createdTime()->format(Date::FORMAT),
            "archived"         => false,
            "has_children"     => false,
            "type"             => "table_of_contents",
            "table_of_contents" => [],
        ];

        $this->assertEquals($expected, $tableOfContents->toArray());
    }

    public function test_no_children_support(): void
    {
        $block = TableOfContents::create();

        $this->expectException(NotionException::class);
        /** @psalm-suppress UnusedMethodCall */
        $block->changeChildren([]);
    }
}
