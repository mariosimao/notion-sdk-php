<?php

namespace Notion\Test\Unit\Common;

use Notion\Common\ParentBlock;
use Notion\Common\ParentType;
use PHPUnit\Framework\TestCase;

class ParentBlockTest extends TestCase
{
    public function test_page(): void
    {
        $id = "5e406817-16ab-4a71-a7d4-e1f0ea5629e4";
        $parent = ParentBlock::page($id);

        $this->assertSame($id, $parent->id);
        $this->assertSame(ParentType::Page, $parent->type);
    }

    public function test_database(): void
    {
        $id = "5e406817-16ab-4a71-a7d4-e1f0ea5629e4";
        $parent = ParentBlock::database($id);

        $this->assertSame($id, $parent->id);
        $this->assertSame(ParentType::Database, $parent->type);
    }

    public function test_block(): void
    {
        $id = "5e406817-16ab-4a71-a7d4-e1f0ea5629e4";
        $parent = ParentBlock::block($id);

        $this->assertSame($id, $parent->id);
        $this->assertSame(ParentType::Block, $parent->type);
    }

    public function test_workspace(): void
    {
        $parent = ParentBlock::workspace();

        $this->assertNull($parent->id);
        $this->assertSame(ParentType::Workspace, $parent->type);
    }

    public function test_array_conversion_page(): void
    {
        $array = [
            "type" => "page_id",
            "page_id" => "c2c2b3c3-edc6-4c2b-950d-de4e0ccdb052",
        ];
        $parent = ParentBlock::fromArray($array);

        $this->assertSame($array, $parent->toArray());
    }

    public function test_array_conversion_database(): void
    {
        $array = [
            "type" => "database_id",
            "database_id" => "c2c2b3c3-edc6-4c2b-950d-de4e0ccdb052",
        ];
        $parent = ParentBlock::fromArray($array);

        $this->assertSame($array, $parent->toArray());
    }

    public function test_array_conversion_block(): void
    {
        $array = [
            "type" => "block_id",
            "block_id" => "c2c2b3c3-edc6-4c2b-950d-de4e0ccdb052",
        ];
        $parent = ParentBlock::fromArray($array);

        $this->assertSame($array, $parent->toArray());
    }

    public function test_array_conversion_workspace(): void
    {
        $array = [
            "type" => "workspace",
            "workspace" => true,
        ];
        $parent = ParentBlock::fromArray($array);

        $this->assertSame($array, $parent->toArray());
    }
}
