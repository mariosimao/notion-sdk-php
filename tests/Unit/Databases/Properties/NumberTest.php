<?php

namespace Notion\Test\Unit\Databases\Properties;

use Notion\Databases\Properties\Factory;
use Notion\Databases\Properties\Number;
use PHPUnit\Framework\TestCase;

class NumberTest extends TestCase
{
    public function test_create(): void
    {
        $price = Number::create("Price", Number::FORMAT_DOLLAR);

        $this->assertEquals("Price", $price->property()->name());
        $this->assertEquals("dollar", $price->format());
        $this->assertTrue($price->property()->isNumber());
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
        $fromFactory = Factory::fromArray($array);

        $this->assertEquals($array, $number->toArray());
        $this->assertEquals($array, $fromFactory->toArray());
    }

    public function test_change_format(): void
    {
        $price = Number::create("Price", Number::FORMAT_DOLLAR)->withFormat(Number::FORMAT_EURO);

        $this->assertEquals("euro", $price->format());
    }
}
