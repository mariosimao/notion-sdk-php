<?php

namespace Notion\Test\Unit\Databases\Properties;

use Notion\Databases\Properties\PropertyFactory;
use Notion\Databases\Properties\Checkbox;
use Notion\Databases\Properties\PropertyType;
use PHPUnit\Framework\TestCase;

class CheckboxTest extends TestCase
{
    public function test_create(): void
    {
        $checkbox = Checkbox::create("Dummy prop name");

        $this->assertEquals("Dummy prop name", $checkbox->metadata()->name);
        $this->assertEquals(PropertyType::Checkbox, $checkbox->metadata()->type);
    }

    public function test_array_conversion(): void
    {
        $array = [
            "id"    => "abc",
            "name"  => "dummy",
            "type"  => "checkbox",
            "checkbox" => new \stdClass(),
        ];
        $checkbox = Checkbox::fromArray($array);
        $fromFactory = PropertyFactory::fromArray($array);

        $this->assertEquals($array, $checkbox->toArray());
        $this->assertEquals($array, $fromFactory->toArray());
    }
}
