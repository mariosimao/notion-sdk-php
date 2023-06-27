<?php

namespace Notion\Test\Unit\Pages\Properties;

use Notion\Pages\Properties\Email;
use Notion\Pages\Properties\PropertyFactory;
use Notion\Pages\Properties\PropertyType;
use PHPUnit\Framework\TestCase;

class EmailTest extends TestCase
{
    public function test_create(): void
    {
        $email = Email::create("mario@domain.com");

        $this->assertEquals(PropertyType::Email, $email->metadata()->type);
        $this->assertEquals("mario@domain.com", $email->email);
    }

    public function test_create_empty(): void
    {
        $email = Email::createEmpty();

        $this->assertTrue($email->isEmpty());
    }

    public function test_change_email(): void
    {
        $email = Email::create("mario@domain.com")->changeEmail("luigi@domain.com");

        $this->assertEquals("luigi@domain.com", $email->email);
    }

    public function test_clear(): void
    {
        $email = Email::create("mario@domain.com")->clear();

        $this->assertTrue($email->isEmpty());
    }

    public function test_array_conversion(): void
    {
        $array = [
            "id" => "abc",
            "type" => "email",
            "email" => "mario@domain.com",
        ];

        $email = Email::fromArray($array);
        $fromFactory = PropertyFactory::fromArray($array);

        $this->assertEquals($array, $email->toArray());
        $this->assertEquals($array, $fromFactory->toArray());
    }

    public function test_is_empty(): void
    {
        $array = [
            "id" => "abc",
            "type" => "email",
            "email" => null,
        ];

        $email = Email::fromArray($array);

        $this->assertTrue($email->isEmpty());
    }
}
