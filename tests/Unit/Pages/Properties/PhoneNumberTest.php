<?php

namespace Notion\Test\Unit\Pages\Properties;

use Notion\Pages\Properties\PhoneNumber;
use Notion\Pages\Properties\Factory;
use PHPUnit\Framework\TestCase;

class PhoneNumberTest extends TestCase
{
    public function test_create(): void
    {
        $phone = PhoneNumber::create("415-000-1111");

        $this->assertTrue($phone->property()->isPhoneNumber());
        $this->assertEquals("415-000-1111", $phone->phone());
    }

    public function test_change_phone(): void
    {
        $phone = PhoneNumber::create("415-000-1111")->withPhone("415-000-2222");

        $this->assertEquals("415-000-2222", $phone->phone());
    }

    public function test_array_conversion(): void
    {
        $array = [
            "id" => "abc",
            "type" => "phone_number",
            "phone_number" => "415-000-1111",
        ];

        $phone = PhoneNumber::fromArray($array);
        $fromFactory = Factory::fromArray($array);

        $this->assertEquals($array, $phone->toArray());
        $this->assertEquals($array, $fromFactory->toArray());
    }
}
