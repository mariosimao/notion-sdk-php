<?php

namespace Notion\Test\Unit\Databases\Properties;

use Notion\Databases\Properties\Factory;
use Notion\Databases\Properties\People;
use PHPUnit\Framework\TestCase;

class PeopleTest extends TestCase
{
    public function test_create(): void
    {
        $people = People::create("Dummy prop name");

        $this->assertEquals("Dummy prop name", $people->property()->name());
        $this->assertTrue($people->property()->isPeople());
    }

    public function test_array_conversion(): void
    {
        $array = [
            "id"    => "abc",
            "name"  => "dummy",
            "type"  => "people",
            "people" => new \stdClass(),
        ];
        $people = People::fromArray($array);
        $fromFactory = Factory::fromArray($array);

        $this->assertEquals($array, $people->toArray());
        $this->assertEquals($array, $fromFactory->toArray());
    }
}
