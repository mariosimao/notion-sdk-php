<?php

namespace Notion\Test\Unit\Databases\Properties;

use Notion\Databases\Properties\PropertyFactory;
use Notion\Databases\Properties\Files;
use Notion\Databases\Properties\PropertyType;
use PHPUnit\Framework\TestCase;

class FilesTest extends TestCase
{
    public function test_create(): void
    {
        $files = Files::create("Dummy prop name");

        $this->assertEquals("Dummy prop name", $files->metadata()->name);
        $this->assertEquals(PropertyType::Files, $files->metadata()->type);
    }

    public function test_array_conversion(): void
    {
        $array = [
            "id"    => "abc",
            "name"  => "dummy",
            "type"  => "files",
            "files" => new \stdClass(),
        ];
        $files = Files::fromArray($array);
        $fromFactory = PropertyFactory::fromArray($array);

        $this->assertEquals($array, $files->toArray());
        $this->assertEquals($array, $fromFactory->toArray());
    }
}
