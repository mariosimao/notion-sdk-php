<?php

namespace Notion\Test\Unit\Pages\Properties;

use Notion\Pages\Properties\LastEditedBy;
use Notion\Pages\Properties\PropertyFactory;
use Notion\Pages\Properties\PropertyType;
use PHPUnit\Framework\TestCase;

class LastEditedByTest extends TestCase
{
    public function test_array_conversion(): void
    {
        $array = [
            "id" => "abc",
            "type" => "last_edited_by",
            "last_edited_by" => [
                "object"     => "user",
                "id" => "62e1fd10-8b04-41eb-97c1-d2deddd160d4",
                "name" => "Mario",
                "type" => "person",
                "person" => [ "email" => "mario@domain.com" ],
            ],
        ];

        $createdBy = LastEditedBy::fromArray($array);
        $fromFactory = PropertyFactory::fromArray($array);

        $this->assertEquals($array, $createdBy->toArray());
        $this->assertEquals($array, $fromFactory->toArray());
        $this->assertEquals("Mario", $createdBy->user->name);
        $this->assertEquals(PropertyType::LastEditedBy, $createdBy->metadata()->type);
    }
}
