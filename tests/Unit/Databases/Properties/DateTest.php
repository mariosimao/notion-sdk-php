<?php

namespace Notion\Test\Unit\Databases\Properties;

use Notion\Databases\Properties\Factory;
use Notion\Databases\Properties\Date;
use PHPUnit\Framework\TestCase;

class DateTest extends TestCase
{
    public function test_create(): void
    {
        $title = Date::create("Dummy prop name");

        $this->assertEquals("Dummy prop name", $title->property()->name());
        $this->assertTrue($title->property()->isDate());
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
        $fromFactory = Factory::fromArray($array);

        $this->assertEquals($array, $date->toArray());
        $this->assertEquals($array, $fromFactory->toArray());
    }
}
