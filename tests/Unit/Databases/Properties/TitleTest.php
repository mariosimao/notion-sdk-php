<?php

namespace Notion\Test\Unit\Databases\Properties;

use Notion\Databases\Properties\Factory;
use Notion\Databases\Properties\Title;
use PHPUnit\Framework\TestCase;

class TitleTest extends TestCase
{
    public function test_create(): void
    {
        $title = Title::create("Dummy prop name");

        $this->assertEquals("Dummy prop name", $title->property()->name());
        $this->assertTrue($title->property()->isTitle());
        $this->assertEmpty($title->property()->id());
        $this->assertEquals("title", $title->property()->type());
    }

    public function test_array_conversion(): void
    {
        $array = [
            "id"    => "title",
            "name"  => "dummy",
            "type"  => "title",
            "title" => [],
        ];
        $title = Title::fromArray($array);
        $fromFactory = Factory::fromArray($array);

        $this->assertEquals($array, $title->toArray());
        $this->assertEquals($array, $fromFactory->toArray());
    }
}
