<?php

namespace Notion\Test\Unit\Blocks;

use Notion\Blocks\BlockFactory;
use Notion\Blocks\ChildDatabase;
use Notion\Blocks\Exceptions\BlockTypeException;
use Notion\Common\Date;
use Notion\NotionException;
use PHPUnit\Framework\TestCase;

class ChildDatabaseTest extends TestCase
{
    public function test_create_empty_heading(): void
    {
        $heading = ChildDatabase::create();

        $this->assertEmpty($heading->databaseTitle());
    }

    public function test_create_from_string(): void
    {
        $heading = ChildDatabase::fromString("Database title");

        $this->assertEquals("Database title", $heading->databaseTitle());
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
            "type"             => "child_database",
            "child_database"   => [ "title" => "Database title" ],
        ];

        $childDatabase = ChildDatabase::fromArray($array);

        $this->assertEquals("Database title", $childDatabase->databaseTitle());
        $this->assertFalse($childDatabase->block()->archived());

        $this->assertEquals($childDatabase, BlockFactory::fromArray($array));
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
            "child_database"   => [ "title" => "Wrong array" ],
        ];

        ChildDatabase::fromArray($array);
    }

    public function test_transform_in_array(): void
    {
        $childDatabase = ChildDatabase::fromString("Database title");

        $expected = [
            "object"           => "block",
            "created_time"     => $childDatabase->block()->createdTime()->format(Date::FORMAT),
            "last_edited_time" => $childDatabase->block()->createdTime()->format(Date::FORMAT),
            "archived"         => false,
            "has_children"     => false,
            "type"             => "child_database",
            "child_database"   => [ "title" => "Database title" ],
        ];

        $this->assertEquals($expected, $childDatabase->toArray());
    }

    public function test_replace_page_title(): void
    {
        $oldHeading = ChildDatabase::fromString("Database 1");

        $newHeading = $oldHeading->withDatabaseTitle("Database 2");

        $this->assertEquals("Database 1", $oldHeading->databaseTitle());
        $this->assertEquals("Database 2", $newHeading->databaseTitle());
    }

    public function test_no_children_support(): void
    {
        $block = ChildDatabase::create();

        $this->expectException(NotionException::class);
        /** @psalm-suppress UnusedMethodCall */
        $block->changeChildren([]);
    }

    public function test_array_for_update_operations(): void
    {
        $block = ChildDatabase::create();

        $array = $block->toUpdateArray();

        $this->assertCount(2, $array);
    }

    public function test_archive(): void
    {
        $block = ChildDatabase::create();

        $block = $block->archive();

        $this->assertTrue($block->block()->archived());
    }
}
