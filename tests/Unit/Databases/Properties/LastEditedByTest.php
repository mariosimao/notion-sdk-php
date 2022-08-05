<?php

namespace Notion\Test\Unit\Databases\Properties;

use Notion\Databases\Properties\Factory;
use Notion\Databases\Properties\LastEditedBy;
use PHPUnit\Framework\TestCase;

class LastEditedByTest extends TestCase
{
    public function test_create(): void
    {
        $lastEditedBy = LastEditedBy::create("Dummy prop name");

        $this->assertEquals("Dummy prop name", $lastEditedBy->property()->name());
        $this->assertTrue($lastEditedBy->property()->isLastEditedBy());
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
        $fromFactory = Factory::fromArray($array);

        $this->assertEquals($array, $lastEditedBy->toArray());
        $this->assertEquals($array, $fromFactory->toArray());
    }
}
