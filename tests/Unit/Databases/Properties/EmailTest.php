<?php

namespace Notion\Test\Unit\Databases\Properties;

use Notion\Databases\Properties\PropertyFactory;
use Notion\Databases\Properties\Email;
use Notion\Databases\Properties\PropertyType;
use PHPUnit\Framework\TestCase;

class EmailTest extends TestCase
{
    public function test_create(): void
    {
        $email = Email::create("Dummy prop name");

        $this->assertEquals("Dummy prop name", $email->metadata()->name);
        $this->assertEquals(PropertyType::Email, $email->metadata()->type);
    }

    public function test_array_conversion(): void
    {
        $array = [
            "id"    => "abc",
            "name"  => "dummy",
            "type"  => "email",
            "email" => new \stdClass(),
        ];
        $email = Email::fromArray($array);
        $fromFactory = PropertyFactory::fromArray($array);

        $this->assertEquals($array, $email->toArray());
        $this->assertEquals($array, $fromFactory->toArray());
    }
}
