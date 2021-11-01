<?php

namespace Notion\Test\Unit\Databases\Properties;

use Notion\Databases\Properties\Factory;
use Notion\Databases\Properties\Email;
use PHPUnit\Framework\TestCase;

class EmailTest extends TestCase
{
    public function test_create(): void
    {
        $email = Email::create("Dummy prop name");

        $this->assertEquals("Dummy prop name", $email->property()->name());
        $this->assertTrue($email->property()->isEmail());
    }

    public function test_array_conversion(): void
    {
        $array = [
            "id"    => "abc",
            "name"  => "dummy",
            "type"  => "email",
            "email" => [],
        ];
        $email = Email::fromArray($array);
        $fromFactory = Factory::fromArray($array);

        $this->assertEquals($array, $email->toArray());
        $this->assertEquals($array, $fromFactory->toArray());
    }
}
