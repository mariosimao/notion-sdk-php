<?php

namespace Notion\Test\Unit\Databases\Properties;

use Notion\Databases\Properties\PropertyFactory;
use Notion\Databases\Properties\CreatedBy;
use Notion\Databases\Properties\PropertyType;
use PHPUnit\Framework\TestCase;

class CreatedByTest extends TestCase
{
    public function test_create(): void
    {
        $createdBy = CreatedBy::create("Dummy prop name");

        $this->assertEquals("Dummy prop name", $createdBy->metadata()->name);
        $this->assertEquals(PropertyType::CreatedBy, $createdBy->metadata()->type);
    }

    public function test_array_conversion(): void
    {
        $array = [
            "id"    => "abc",
            "name"  => "dummy",
            "type"  => "created_by",
            "created_by" => new \stdClass(),
        ];
        $createdBy = CreatedBy::fromArray($array);
        $fromFactory = PropertyFactory::fromArray($array);

        $this->assertEquals($array, $createdBy->toArray());
        $this->assertEquals($array, $fromFactory->toArray());
    }
}
