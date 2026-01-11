<?php

namespace Notion\Test\Unit\DataSources\Properties;

use Notion\DataSources\Properties\PropertyFactory;
use Notion\DataSources\Properties\People;
use Notion\DataSources\Properties\PropertyType;
use PHPUnit\Framework\TestCase;

class PeopleTest extends TestCase
{
    public function test_create(): void
    {
        $people = People::create("Dummy prop name");

        $this->assertEquals("Dummy prop name", $people->metadata()->name);
        $this->assertEquals(PropertyType::People, $people->metadata()->type);
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
        $fromFactory = PropertyFactory::fromArray($array);

        $this->assertEquals($array, $people->toArray());
        $this->assertEquals($array, $fromFactory->toArray());
    }
}
