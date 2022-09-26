<?php

namespace Notion\Test\Unit\Databases\Properties;

use Notion\Databases\Properties\PropertyFactory;
use Notion\Databases\Properties\PropertyType;
use Notion\Databases\Properties\Title;
use PHPUnit\Framework\TestCase;

class TitleTest extends TestCase
{
    public function test_create(): void
    {
        $title = Title::create("Dummy prop name");

        $this->assertEquals("Dummy prop name", $title->metadata()->name);
        $this->assertEmpty($title->metadata()->id);
        $this->assertEquals(PropertyType::Title, $title->metadata()->type);
    }

    public function test_array_conversion(): void
    {
        $array = [
            "id"    => "title",
            "name"  => "dummy",
            "type"  => "title",
            "title" => new \stdClass(),
        ];
        $title = Title::fromArray($array);
        $fromFactory = PropertyFactory::fromArray($array);

        $this->assertEquals($array, $title->toArray());
        $this->assertEquals($array, $fromFactory->toArray());
    }
}
