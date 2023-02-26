<?php

namespace Notion\Test\Unit\Databases\Properties;

use Notion\Databases\Properties\PropertyFactory;
use Notion\Databases\Properties\PropertyType;
use Notion\Databases\Properties\Unknown;
use PHPUnit\Framework\TestCase;

class UnknownTest extends TestCase
{
    public function test_serialization(): void
    {
        $array = [
            "id"    => "abc",
            "name"  => "dummy",
            "type"  => "blabla",
            "blabla" => new \stdClass(),
        ];
        $property = Unknown::fromArray($array);
        $fromFactory = PropertyFactory::fromArray($array);

        $this->assertEquals($array, $property->toArray());
        $this->assertEquals($array, $fromFactory->toArray());
        $this->assertSame(PropertyType::Unknown, $property->metadata()->type);
    }
}
