<?php

namespace Notion\Test\Unit\Databases;

use Notion\Databases\DatabaseParent;
use PHPUnit\Framework\TestCase;

class DatabaseParentTest extends TestCase
{
    public function test_create_parent_page(): void
    {
        $parent = DatabaseParent::page("058d158b-09de-4d69-be07-901c20a7ca5c");

        $this->assertTrue($parent->isPage());
        $this->assertEquals("058d158b-09de-4d69-be07-901c20a7ca5c", $parent->id);
    }

    public function test_create_parent_workspace(): void
    {
        $parent = DatabaseParent::workspace();

        $this->assertTrue($parent->isWorkspace());
        $this->assertEquals("workspace", $parent->type->value);
    }

    public function test_create_parent_block(): void
    {
        $parent = DatabaseParent::block("0181c3aa-1112-489f-b34a-515b4e3583ed");

        $this->assertTrue($parent->isBlock());
        $this->assertSame("0181c3aa-1112-489f-b34a-515b4e3583ed", $parent->id);
    }

    public function test_page_array_conversion(): void
    {
        $array = [
            "type" => "page_id",
            "page_id" => "7a774b5d-ca74-4679-9f18-689b5a98f138",
        ];
        $parent = DatabaseParent::fromArray($array);

        $this->assertEquals($array["page_id"], $parent->toArray()["page_id"]);
    }

    public function test_workspace_array_conversion(): void
    {
        $array = [
            "type" => "workspace",
            "workspace" => true,
        ];
        $parent = DatabaseParent::fromArray($array);

        $this->assertEquals($array["workspace"], $parent->toArray()["workspace"]);
    }

    public function test_block_array_conversion(): void
    {
        $array = [
            "type" => "block_id",
            "block_id" => "7a774b5d-ca74-4679-9f18-689b5a98f138",
        ];
        $parent = DatabaseParent::fromArray($array);

        $this->assertEquals($array["block_id"], $parent->toArray()["block_id"]);
    }

    public function test_invalid_type_array(): void
    {
        $this->expectException(\ValueError::class);
        /** @psalm-suppress InvalidArgument */
        DatabaseParent::fromArray([ "type" => "invalid-type" ]);
    }
}
