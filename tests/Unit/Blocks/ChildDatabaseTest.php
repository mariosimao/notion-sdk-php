<?php

namespace Notion\Test\Unit\Blocks;

use Notion\Blocks\BlockFactory;
use Notion\Blocks\ChildDatabase;
use Notion\Blocks\Paragraph;
use Notion\Exceptions\BlockException;
use PHPUnit\Framework\TestCase;

class ChildDatabaseTest extends TestCase
{
    public function test_array_conversion(): void
    {
        $array = [
            "object"           => "block",
            "id"               => "04a13895-f072-4814-8af7-cd11af127040",
            "created_time"     => "2021-10-18T17:09:00.000000Z",
            "last_edited_time" => "2021-10-18T17:09:00.000000Z",
            "archived"         => false,
            "has_children"     => false,
            "type"             => "child_database",
            "child_database"   => [ "title" => "Database title" ],
        ];

        $childDatabase = ChildDatabase::fromArray($array);

        $this->assertEquals("Database title", $childDatabase->title);
        $this->assertFalse($childDatabase->metadata()->archived);

        $this->assertEquals($childDatabase, BlockFactory::fromArray($array));
        $this->assertEquals($array, $childDatabase->toArray());
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

    public function test_not_allow_to_change_children(): void
    {
        $block = ChildDatabase::fromArray($this->mockArray());

        $this->expectException(BlockException::class);
        /** @psalm-suppress UnusedMethodCall */
        $block->changeChildren(
            Paragraph::fromString("Sample paragraph.")
        );
    }

    public function test_not_allow_to_add_child(): void
    {
        $block = ChildDatabase::fromArray($this->mockArray());

        $this->expectException(BlockException::class);
        /** @psalm-suppress UnusedMethodCall */
        $block->addChild(
            Paragraph::fromString("Sample paragraph.")
        );
    }

    public function test_archive(): void
    {
        $block = ChildDatabase::fromArray($this->mockArray());

        $block = $block->archive();

        $this->assertTrue($block->metadata()->archived);
    }

    private function mockArray(): array
    {
        return [
            "object"           => "block",
            "id"               => "04a13895-f072-4814-8af7-cd11af127040",
            "created_time"     => "2021-10-18T17:09:00.000Z",
            "last_edited_time" => "2021-10-18T17:09:00.000Z",
            "archived"         => false,
            "has_children"     => false,
            "type"             => "child_database",
            "child_database"   => [ "title" => "Database title" ],
        ];
    }
}
