<?php

namespace Notion\Test\Unit\Databases\Properties;

use Notion\Databases\Properties\Factory;
use Notion\Databases\Properties\Files;
use PHPUnit\Framework\TestCase;

class FilesTest extends TestCase
{
    public function test_create(): void
    {
        $files = Files::create("Dummy prop name");

        $this->assertEquals("Dummy prop name", $files->property()->name());
        $this->assertTrue($files->property()->isFiles());
    }

    public function test_array_conversion(): void
    {
        $array = [
            "id"    => "abc",
            "name"  => "dummy",
            "type"  => "files",
            "files" => [],
        ];
        $files = Files::fromArray($array);
        $fromFactory = Factory::fromArray($array);

        $this->assertEquals($array, $files->toArray());
        $this->assertEquals($array, $fromFactory->toArray());
    }
}
