<?php

namespace Notion\Test\Unit\Databases\Properties;

use Notion\Databases\Properties\PropertyFactory;
use Notion\Databases\Properties\PropertyType;
use Notion\Databases\Properties\UniqueId;
use PHPUnit\Framework\TestCase;

class UniqueIdTest extends TestCase
{
    public function test_array_conversion(): void
    {
        $array = [
            "id"    => "abc",
            "name"  => "dummy",
            "type"  => "unique_id",
            "unique_id" => new \stdClass(),
        ];
        $prop = UniqueId::fromArray($array);
        $fromFactory = PropertyFactory::fromArray($array);

        $this->assertEquals($array, $prop->toArray());
        $this->assertSame(PropertyType::UniqueId, $prop->metadata()->type);
        $this->assertEquals($array, $fromFactory->toArray());
    }
}
