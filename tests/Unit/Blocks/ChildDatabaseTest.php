<?php

namespace Notion\Test\Unit\Blocks;

use Notion\Blocks\BlockFactory;
use Notion\Blocks\ChildDatabase;
use Notion\Exceptions\BlockException;
use Notion\Common\Date;
use Notion\NotionException;
use PHPUnit\Framework\TestCase;

class ChildDatabaseTest extends TestCase
{
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

        $this->assertEquals("Database title", $childDatabase->databaseTitle);
        $this->assertFalse($childDatabase->metadata()->archived);

        $this->assertEquals($childDatabase, BlockFactory::fromArray($array));
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
            "child_database"   => [ "title" => "Wrong array" ],
        ];

        ChildDatabase::fromArray($array);
    }
}
