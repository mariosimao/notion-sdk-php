<?php

namespace Notion\Test\Unit\Pages\Properties;

use Notion\Common\RichText;
use Notion\Pages\Properties\PropertyFactory;
use Notion\Pages\Properties\Number;
use Notion\Pages\Properties\PropertyType;
use PHPUnit\Framework\TestCase;

class NumberTest extends TestCase
{
    public function test_create(): void
    {
        $number = Number::create(123);

        $this->assertEquals(123, $number->number);
        $this->assertEquals("", $number->metadata()->id);
        $this->assertEquals(PropertyType::Number, $number->metadata()->type);
    }

    public function test_create_empty(): void
    {
        $number = Number::createEmpty();

        $this->assertTrue($number->isEmpty());
    }

    public function test_array_conversion(): void
    {
        $array = [
            "id"    => "a7ede3b7-c7ae-4eb8-b415-a7f80ac4dfe5",
            "type"  => "number",
            "number" => 123,
        ];

        $number = Number::fromArray($array);
        $fromFactory = PropertyFactory::fromArray($array);

        $this->assertEquals($array, $number->toArray());
        $this->assertEquals($array, $fromFactory->toArray());
    }

    public function test_change_value(): void
    {
        $number = Number::create(123)->changeNumber(0.25);
        $this->assertEquals(0.25, $number->number);
    }

    public function test_clear(): void
    {
        $number = Number::create(123)->clear();

        $this->assertTrue($number->isEmpty());
    }

    public function test_is_empty(): void
    {
        $array = [
            "id"    => "a7ede3b7-c7ae-4eb8-b415-a7f80ac4dfe5",
            "type"  => "number",
            "number" => null,
        ];

        $number = Number::fromArray($array);

        $this->assertTrue($number->isEmpty());
    }
}
