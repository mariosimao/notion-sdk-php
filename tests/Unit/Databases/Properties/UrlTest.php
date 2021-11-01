<?php

namespace Notion\Test\Unit\Databases\Properties;

use Notion\Databases\Properties\Factory;
use Notion\Databases\Properties\Url;
use PHPUnit\Framework\TestCase;

class UrlTest extends TestCase
{
    public function test_create(): void
    {
        $url = Url::create("Dummy prop name");

        $this->assertEquals("Dummy prop name", $url->property()->name());
        $this->assertTrue($url->property()->isUrl());
    }

    public function test_array_conversion(): void
    {
        $array = [
            "id"    => "abc",
            "name"  => "dummy",
            "type"  => "url",
            "url" => [],
        ];
        $url = Url::fromArray($array);
        $fromFactory = Factory::fromArray($array);

        $this->assertEquals($array, $url->toArray());
        $this->assertEquals($array, $fromFactory->toArray());
    }
}
