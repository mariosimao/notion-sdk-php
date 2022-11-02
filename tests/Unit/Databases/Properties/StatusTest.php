<?php

namespace Notion\Test\Unit\Databases\Properties;

use Notion\Databases\Properties\PropertyFactory;
use Notion\Databases\Properties\PropertyType;
use Notion\Databases\Properties\Status;
use PHPUnit\Framework\TestCase;

class StatusTest extends TestCase
{
    public function test_array_conversion(): void
    {
        $array = [
            "id"    => "abc",
            "name"  => "Status",
            "type"  => "status",
            "status" => [
                "groups" => [
                    [ "id" => "111", "option_ids" => ["aaa"], "color" => "green", "name" => "To-do" ],
                    [ "id" => "222", "option_ids" => ["bbb"], "color" => "yellow", "name" => "In Progress" ],
                    [ "id" => "333", "option_ids" => ["ccc"], "color" => "red", "name" => "Complete" ],
                ],
                "options" => [
                    [ "id" => "aaa", "name" => "Option A", "color" => "default" ],
                    [ "id" => "bbb", "name" => "Option B", "color" => "default" ],
                    [ "id" => "ccc", "name" => "Option C", "color" => "default" ],
                ],
            ],
        ];
        $status = Status::fromArray($array);
        $fromFactory = PropertyFactory::fromArray($array);

        $this->assertEquals($array, $status->toArray());
        $this->assertEquals($array, $fromFactory->toArray());
        $this->assertSame(PropertyType::Status, $status->metadata()->type);
    }
}
