<?php

namespace Notion\Test\Unit\Databases\Properties;

use Notion\Databases\Properties\PropertyFactory;
use Notion\Databases\Properties\PhoneNumber;
use Notion\Databases\Properties\PropertyType;
use PHPUnit\Framework\TestCase;

class PhoneNumberTest extends TestCase
{
    public function test_create(): void
    {
        $phoneNumber = PhoneNumber::create("Dummy prop name");

        $this->assertEquals("Dummy prop name", $phoneNumber->metadata()->name);
        $this->assertEquals(PropertyType::PhoneNumber, $phoneNumber->metadata()->type);
    }

    public function test_array_conversion(): void
    {
        $array = [
            "id"    => "abc",
            "name"  => "dummy",
            "type"  => "phone_number",
            "phone_number" => new \stdClass(),
        ];
        $phoneNumber = PhoneNumber::fromArray($array);
        $fromFactory = PropertyFactory::fromArray($array);

        $this->assertEquals($array, $phoneNumber->toArray());
        $this->assertEquals($array, $fromFactory->toArray());
    }
}
