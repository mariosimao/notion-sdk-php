<?php

namespace Notion\Test\Unit\Pages\Properties;

use Notion\Pages\Properties\CreatedBy;
use Notion\Pages\Properties\PropertyFactory;
use Notion\Pages\Properties\PropertyType;
use PHPUnit\Framework\TestCase;

class CreatedByTest extends TestCase
{
    public function test_array_conversion(): void
    {
        $array = [
            "id" => "abc",
            "type" => "created_by",
            "created_by" => [
                "object"     => "user",
                "id" => "62e1fd10-8b04-41eb-97c1-d2deddd160d4",
                "name" => "Mario",
                "type" => "person",
                "person" => [ "email" => "mario@domain.com" ],
            ],
        ];

        $createdBy = CreatedBy::fromArray($array);
        $fromFactory = PropertyFactory::fromArray($array);

        $this->assertEquals($array, $createdBy->toArray());
        $this->assertEquals($array, $fromFactory->toArray());
        $this->assertEquals("Mario", $createdBy->user->name);
        $this->assertEquals(PropertyType::CreatedBy, $createdBy->metadata()->type);
    }
}
