<?php

namespace Notion\Test\Unit\Databases\Properties;

use Notion\Databases\Properties\Factory;
use Notion\Databases\Properties\PhoneNumber;
use PHPUnit\Framework\TestCase;

class PhoneNumberTest extends TestCase
{
    public function test_create(): void
    {
        $phoneNumber = PhoneNumber::create("Dummy prop name");

        $this->assertEquals("Dummy prop name", $phoneNumber->property()->name());
        $this->assertTrue($phoneNumber->property()->isPhoneNumber());
    }

    public function test_array_conversion(): void
    {
        $array = [
            "id"    => "abc",
            "name"  => "dummy",
            "type"  => "phone_number",
            "phone_number" => [],
        ];
        $phoneNumber = PhoneNumber::fromArray($array);
        $fromFactory = Factory::fromArray($array);

        $this->assertEquals($array, $phoneNumber->toArray());
        $this->assertEquals($array, $fromFactory->toArray());
    }
}
