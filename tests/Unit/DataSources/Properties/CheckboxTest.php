<?php

namespace Notion\Test\Unit\DataSources\Properties;

use Notion\DataSources\Properties\PropertyFactory;
use Notion\DataSources\Properties\Checkbox;
use Notion\DataSources\Properties\PropertyType;
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
