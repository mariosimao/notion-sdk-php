<?php

namespace Notion\Test\Unit\Databases\Properties;

use Notion\Databases\Properties\Factory;
use Notion\Databases\Properties\CreatedBy;
use PHPUnit\Framework\TestCase;

class CreatedByTest extends TestCase
{
    public function test_create(): void
    {
        $createdBy = CreatedBy::create("Dummy prop name");

        $this->assertEquals("Dummy prop name", $createdBy->property()->name());
        $this->assertTrue($createdBy->property()->isCreatedBy());
    }

    public function test_array_conversion(): void
    {
        $array = [
            "id"    => "abc",
            "name"  => "dummy",
            "type"  => "created_by",
            "created_by" => [],
        ];
        $createdBy = CreatedBy::fromArray($array);
        $fromFactory = Factory::fromArray($array);

        $this->assertEquals($array, $createdBy->toArray());
        $this->assertEquals($array, $fromFactory->toArray());
    }
}
