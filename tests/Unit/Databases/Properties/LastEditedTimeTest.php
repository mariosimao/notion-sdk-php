<?php

namespace Notion\Test\Unit\Databases\Properties;

use Notion\Databases\Properties\PropertyFactory;
use Notion\Databases\Properties\LastEditedTime;
use Notion\Databases\Properties\PropertyType;
use PHPUnit\Framework\TestCase;

class LastEditedTimeTest extends TestCase
{
    public function test_create(): void
    {
        $lastEditedTime = LastEditedTime::create("Dummy prop name");

        $this->assertEquals("Dummy prop name", $lastEditedTime->metadata()->name);
        $this->assertEquals(PropertyType::LastEditedTime, $lastEditedTime->metadata()->type);
    }

    public function test_array_conversion(): void
    {
        $array = [
            "id"    => "abc",
            "name"  => "dummy",
            "type"  => "last_edited_time",
            "last_edited_time" => new \stdClass(),
        ];
        $lastEditedTime = LastEditedTime::fromArray($array);
        $fromFactory = PropertyFactory::fromArray($array);

        $this->assertEquals($array, $lastEditedTime->toArray());
        $this->assertEquals($array, $fromFactory->toArray());
    }
}
