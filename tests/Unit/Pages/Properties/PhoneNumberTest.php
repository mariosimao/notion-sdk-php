<?php

namespace Notion\Test\Unit\Pages\Properties;

use Notion\Pages\Properties\PhoneNumber;
use Notion\Pages\Properties\PropertyFactory;
use Notion\Pages\Properties\PropertyType;
use PHPUnit\Framework\TestCase;

class PhoneNumberTest extends TestCase
{
    public function test_create(): void
    {
        $phone = PhoneNumber::create("415-000-1111");

        $this->assertTrue($phone->metadata()->type === PropertyType::PhoneNumber);
        $this->assertEquals("415-000-1111", $phone->phone);
    }

    public function test_create_empty(): void
    {
        $phone = PhoneNumber::createEmpty();

        $this->assertTrue($phone->isEmpty());
    }

    public function test_change_phone(): void
    {
        $phone = PhoneNumber::create("415-000-1111")->changePhone("415-000-2222");

        $this->assertEquals("415-000-2222", $phone->phone);
    }

    public function test_clear(): void
    {
        $phone = PhoneNumber::create("415-000-1111")->clear();

        $this->assertTrue($phone->isEmpty());
    }

    public function test_array_conversion(): void
    {
        $array = [
            "id" => "abc",
            "type" => "phone_number",
            "phone_number" => "415-000-1111",
        ];

        $phone = PhoneNumber::fromArray($array);
        $fromFactory = PropertyFactory::fromArray($array);

        $this->assertEquals($array, $phone->toArray());
        $this->assertEquals($array, $fromFactory->toArray());
    }

    public function test_is_empty(): void
    {
        $array = [
            "id" => "abc",
            "type" => "phone_number",
            "phone_number" => null,
        ];

        $phone = PhoneNumber::fromArray($array);

        $this->assertTrue($phone->isEmpty());
    }
}
