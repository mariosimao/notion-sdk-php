<?php

namespace Notion\Test\Unit\Databases\Properties;

use Notion\Databases\Properties\PropertyFactory;
use Notion\Databases\Properties\PropertyType;
use Notion\Databases\Properties\Url;
use PHPUnit\Framework\TestCase;

class UrlTest extends TestCase
{
    public function test_create(): void
    {
        $url = Url::create("Dummy prop name");

        $this->assertEquals("Dummy prop name", $url->metadata()->name);
        $this->assertEquals(PropertyType::Url, $url->metadata()->type);
    }

    public function test_array_conversion(): void
    {
        $array = [
            "id"    => "abc",
            "name"  => "dummy",
            "type"  => "url",
            "url" => new \stdClass(),
        ];
        $url = Url::fromArray($array);
        $fromFactory = PropertyFactory::fromArray($array);

        $this->assertEquals($array, $url->toArray());
        $this->assertEquals($array, $fromFactory->toArray());
    }
}
