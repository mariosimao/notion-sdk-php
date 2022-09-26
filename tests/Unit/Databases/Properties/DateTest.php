<?php

namespace Notion\Test\Unit\Databases\Properties;

use Notion\Databases\Properties\PropertyFactory;
use Notion\Databases\Properties\Date;
use Notion\Databases\Properties\PropertyType;
use PHPUnit\Framework\TestCase;

class DateTest extends TestCase
{
    public function test_create(): void
    {
        $date = Date::create("Dummy prop name");

        $this->assertEquals("Dummy prop name", $date->metadata()->name);
        $this->assertEquals(PropertyType::Date, $date->metadata()->type);
    }

    public function test_array_conversion(): void
    {
        $array = [
            "id"    => "abc",
            "name"  => "dummy",
            "type"  => "date",
            "date" => new \stdClass(),
        ];
        $date = Date::fromArray($array);
        $fromFactory = PropertyFactory::fromArray($array);

        $this->assertEquals($array, $date->toArray());
        $this->assertEquals($array, $fromFactory->toArray());
    }
}
