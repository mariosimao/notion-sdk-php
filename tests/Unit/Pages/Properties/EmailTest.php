<?php

namespace Notion\Test\Unit\Pages\Properties;

use Notion\Pages\Properties\Email;
use Notion\Pages\Properties\Factory;
use PHPUnit\Framework\TestCase;

class EmailTest extends TestCase
{
    public function test_create(): void
    {
        $email = Email::create("mario@domain.com");

        $this->assertTrue($email->property()->isEmail());
        $this->assertEquals("mario@domain.com", $email->email());
    }

    public function test_change_email(): void
    {
        $email = Email::create("mario@domain.com")->withEmail("luigi@domain.com");

        $this->assertEquals("luigi@domain.com", $email->email());
    }

    public function test_array_conversion(): void
    {
        $array = [
            "id" => "abc",
            "type" => "email",
            "email" => "mario@domain.com",
        ];

        $email = Email::fromArray($array);
        $fromFactory = Factory::fromArray($array);

        $this->assertEquals($array, $email->toArray());
        $this->assertEquals($array, $fromFactory->toArray());
    }
}
