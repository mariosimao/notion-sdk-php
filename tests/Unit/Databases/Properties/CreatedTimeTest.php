<?php

namespace Notion\Test\Unit\Databases\Properties;

use Notion\Databases\Properties\PropertyFactory;
use Notion\Databases\Properties\CreatedTime;
use Notion\Databases\Properties\PropertyType;
use PHPUnit\Framework\TestCase;

class CreatedTimeTest extends TestCase
{
    public function test_create(): void
    {
        $createdTime = CreatedTime::create("Dummy prop name");

        $this->assertEquals("Dummy prop name", $createdTime->metadata()->name);
        $this->assertEquals(PropertyType::CreatedTime, $createdTime->metadata()->type);
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
        $fromFactory = PropertyFactory::fromArray($array);

        $this->assertEquals($array, $createdTime->toArray());
        $this->assertEquals($array, $fromFactory->toArray());
    }
}
