<?php

namespace Notion\Test\Unit\Databases\Properties;

use Notion\Databases\Properties\Factory;
use Notion\Databases\Properties\LastEditedTime;
use PHPUnit\Framework\TestCase;

class LastEditedTimeTest extends TestCase
{
    public function test_create(): void
    {
        $lastEditedTime = LastEditedTime::create("Dummy prop name");

        $this->assertEquals("Dummy prop name", $lastEditedTime->property()->name());
        $this->assertTrue($lastEditedTime->property()->isLastEditedTime());
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
        $fromFactory = Factory::fromArray($array);

        $this->assertEquals($array, $lastEditedTime->toArray());
        $this->assertEquals($array, $fromFactory->toArray());
    }
}
