<?php

namespace Notion\Test\Unit\DataSources\Properties;

use Notion\DataSources\Properties\PropertyFactory;
use Notion\DataSources\Properties\Number;
use Notion\DataSources\Properties\NumberFormat;
use Notion\DataSources\Properties\PropertyType;
use PHPUnit\Framework\TestCase;

class NumberTest extends TestCase
{
    public function test_create(): void
    {
        $price = Number::create("Price", NumberFormat::Dollar);

        $this->assertEquals("Price", $price->metadata()->name);
        $this->assertEquals(NumberFormat::Dollar, $price->format);
        $this->assertEquals(PropertyType::Number, $price->metadata()->type);
    }

    public function test_array_conversion(): void
    {
        $array = [
            "id"    => "abc",
            "name"  => "Price",
            "type"  => "number",
            "number" => [
                "format" => "dollar",
            ],
        ];
        $number = Number::fromArray($array);
        $fromFactory = PropertyFactory::fromArray($array);

        $this->assertEquals($array, $number->toArray());
        $this->assertEquals($array, $fromFactory->toArray());
    }

    public function test_change_format(): void
    {
        $price = Number::create("Price", NumberFormat::Dollar)->changeFormat(NumberFormat::Euro);

        $this->assertEquals(NumberFormat::Euro, $price->format);
    }
}
