<?php

namespace Notion\Test\Unit\Pages\Properties;

use Notion\Pages\Properties\PropertyFactory;
use Notion\Pages\Properties\PropertyType;
use Notion\Pages\Properties\Unknown;
use PHPUnit\Framework\TestCase;

class UnknownTest extends TestCase
{
    public function test_serialization(): void
    {
        $array = [
            "id"    => "abc",
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
