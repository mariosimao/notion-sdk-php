<?php

namespace Notion\Test\Unit\DataSources;

use Notion\DataSources\DataSourceParent;
use PHPUnit\Framework\TestCase;

class DataSourceParentTest extends TestCase
{
    public function test_create_parent_database(): void
    {
        $parent = DataSourceParent::database("058d158b-09de-4d69-be07-901c20a7ca5c");

        $this->assertTrue($parent->isDatabase());
        $this->assertEquals("058d158b-09de-4d69-be07-901c20a7ca5c", $parent->id);
    }

    public function test_create_parent_data_source(): void
    {
        $parent = DataSourceParent::dataSource(
            "058d158b-09de-4d69-be07-901c20a7ca5c",
            "0181c3aa-1112-489f-b34a-515b4e3583ed"
        );

        $this->assertTrue($parent->isDataSource());
        $this->assertEquals("058d158b-09de-4d69-be07-901c20a7ca5c", $parent->id);
        $this->assertEquals("0181c3aa-1112-489f-b34a-515b4e3583ed", $parent->databaseId);
    }

    public function test_page_array_conversion(): void
    {
        $array = [
            "type" => "database_id",
            "database_id" => "7a774b5d-ca74-4679-9f18-689b5a98f138",
        ];
        $parent = DataSourceParent::fromArray($array);

        $this->assertEquals($array["database_id"], $parent->toArray()["database_id"]);
    }

    public function test_data_source_array_conversion(): void
    {
        $array = [
            "type" => "data_source_id",
            "data_source_id" => "7a774b5d-ca74-4679-9f18-689b5a98f138",
            "database_id" => "0181c3aa-1112-489f-b34a-515b4e3583ed",
        ];
        $parent = DataSourceParent::fromArray($array);

        $this->assertEquals($array["data_source_id"], $parent->toArray()["data_source_id"]);
        $this->assertEquals($array["database_id"], $parent->toArray()["database_id"]);
    }

    public function test_invalid_type_array(): void
    {
        $this->expectException(\ValueError::class);
        /** @psalm-suppress InvalidArgument */
        DataSourceParent::fromArray([ "type" => "invalid-type" ]);
    }
}
