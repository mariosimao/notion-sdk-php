<?php

namespace Notion\Test\Unit\Databases\Properties;

use Notion\Databases\Properties\Factory;
use Notion\Databases\Properties\CreatedTime;
use PHPUnit\Framework\TestCase;

class CreatedTimeTest extends TestCase
{
    public function test_create(): void
    {
        $createdTime = CreatedTime::create("Dummy prop name");

        $this->assertEquals("Dummy prop name", $createdTime->property()->name());
        $this->assertTrue($createdTime->property()->isCreatedTime());
    }

    public function test_array_conversion(): void
    {
        $array = [
            "id"    => "abc",
            "name"  => "dummy",
            "type"  => "created_time",
            "created_time" => new \stdClass(),
        ];
        $createdTime = CreatedTime::fromArray($array);
        $fromFactory = Factory::fromArray($array);

        $this->assertEquals($array, $createdTime->toArray());
        $this->assertEquals($array, $fromFactory->toArray());
    }
}
