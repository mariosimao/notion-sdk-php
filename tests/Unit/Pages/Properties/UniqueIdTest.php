<?php

namespace Notion\Test\Unit\Pages\Properties;

use Notion\Pages\Properties\PropertyFactory;
use Notion\Pages\Properties\PropertyType;
use Notion\Pages\Properties\UniqueId;
use PHPUnit\Framework\TestCase;

class UniqueIdTest extends TestCase
{
    public function test_array_conversion(): void
    {
        $array = [
            "id" => "abc",
            "type" => "unique_id",
            "unique_id" => [
                "number" => 3,
                "prefix" => "ISSUE"
            ]
        ];

        $prop = UniqueId::fromArray($array);
        $fromFactory = PropertyFactory::fromArray($array);

        $this->assertEquals($array, $prop->toArray());
        $this->assertEquals($array, $fromFactory->toArray());

        $this->assertSame("ISSUE", $prop->prefix);
        $this->assertSame(3, $prop->number);
        $this->assertSame(PropertyType::UniqueId, $prop->metadata()->type);
    }
}
