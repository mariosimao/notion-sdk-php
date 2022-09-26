<?php

namespace Notion\Test\Unit\Databases\Properties;

use Notion\Databases\Properties\PropertyFactory;
use Notion\Databases\Properties\LastEditedBy;
use Notion\Databases\Properties\PropertyType;
use PHPUnit\Framework\TestCase;

class LastEditedByTest extends TestCase
{
    public function test_create(): void
    {
        $lastEditedBy = LastEditedBy::create("Dummy prop name");

        $this->assertEquals("Dummy prop name", $lastEditedBy->metadata()->name);
        $this->assertEquals(PropertyType::LastEditedBy, $lastEditedBy->metadata()->type);
    }

    public function test_array_conversion(): void
    {
        $array = [
            "id"    => "abc",
            "name"  => "dummy",
            "type"  => "last_edited_by",
            "last_edited_by" => new \stdClass(),
        ];
        $lastEditedBy = LastEditedBy::fromArray($array);
        $fromFactory = PropertyFactory::fromArray($array);

        $this->assertEquals($array, $lastEditedBy->toArray());
        $this->assertEquals($array, $fromFactory->toArray());
    }
}
