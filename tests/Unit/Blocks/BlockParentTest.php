<?php

namespace Notion\Test\Unit\Blocks;

use Notion\Blocks\BlockParent;
use Notion\Blocks\BlockParentType;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class BlockParentTest extends TestCase
{
    public function test_page_parent(): void
    {
        $parent = BlockParent::page("page-id");

        $this->assertSame(BlockParentType::Page, $parent->type);
        $this->assertSame("page-id", $parent->id);
        $this->assertTrue($parent->isPage());
        $this->assertSame(["type" => "page_id", "page_id" => "page-id"], $parent->toArray());
    }

    public function test_data_source_parent(): void
    {
        $parent = BlockParent::dataSource("data-source-id", "database-id");

        $this->assertSame(BlockParentType::DataSource, $parent->type);
        $this->assertSame("data-source-id", $parent->id);
        $this->assertSame("database-id", $parent->databaseId);
        $this->assertTrue($parent->isDataSource());
        $this->assertSame([
            "type"           => "data_source_id",
            "data_source_id" => "data-source-id",
            "database_id"    => "database-id",
        ], $parent->toArray());
    }

    public function test_data_source_parent_without_database_id(): void
    {
        $parent = BlockParent::dataSource("data-source-id");

        $this->assertNull($parent->databaseId);
        $this->assertSame(["type" => "data_source_id", "data_source_id" => "data-source-id"], $parent->toArray());
    }

    public function test_database_parent(): void
    {
        $parent = BlockParent::database("database-id");

        $this->assertSame(BlockParentType::Database, $parent->type);
        $this->assertSame("database-id", $parent->id);
        $this->assertTrue($parent->isDatabase());
        $this->assertSame(["type" => "database_id", "database_id" => "database-id"], $parent->toArray());
    }

    public function test_block_parent(): void
    {
        $parent = BlockParent::block("block-id");

        $this->assertSame(BlockParentType::Block, $parent->type);
        $this->assertSame("block-id", $parent->id);
        $this->assertTrue($parent->isBlock());
        $this->assertFalse($parent->isPage());
        $this->assertSame(["type" => "block_id", "block_id" => "block-id"], $parent->toArray());
    }

    public function test_agent_parent(): void
    {
        $parent = BlockParent::agent("agent-id");

        $this->assertSame(BlockParentType::Agent, $parent->type);
        $this->assertSame("agent-id", $parent->id);
        $this->assertTrue($parent->isAgent());
        $this->assertSame(["type" => "agent_id", "agent_id" => "agent-id"], $parent->toArray());
    }

    /** @param array{ type: "page_id"|"data_source_id"|"database_id"|"block_id"|"agent_id" } $array */
    #[DataProvider("parentArrays")]
    public function test_array_conversion(array $array): void
    {
        $parent = BlockParent::fromArray($array);

        $this->assertSame($array, $parent->toArray());
    }

    /** @return array<string, array{ array<string, string> }> */
    public static function parentArrays(): array
    {
        return [
            "page"        => [["type" => "page_id", "page_id" => "page-id"]],
            "data source" => [[
                "type"           => "data_source_id",
                "data_source_id" => "data-source-id",
                "database_id"    => "database-id",
            ]],
            "database"    => [["type" => "database_id", "database_id" => "database-id"]],
            "block"       => [["type" => "block_id", "block_id" => "block-id"]],
            "agent"       => [["type" => "agent_id", "agent_id" => "agent-id"]],
        ];
    }
}
