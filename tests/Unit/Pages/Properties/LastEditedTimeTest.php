<?php

namespace Notion\Test\Unit\Pages\Properties;

use Notion\Pages\Properties\LastEditedTime;
use Notion\Pages\Properties\PropertyFactory;
use Notion\Pages\Properties\PropertyType;
use PHPUnit\Framework\TestCase;

class LastEditedTimeTest extends TestCase
{
    public function test_array_conversion(): void
    {
        $array = [
            "id" => "abc",
            "type" => "last_edited_time",
            "last_edited_time" => "2021-01-01T00:00:00.000000Z",
        ];

        $time = LastEditedTime::fromArray($array);
        $fromFactory = PropertyFactory::fromArray($array);

        $this->assertEquals($array, $time->toArray());
        $this->assertEquals($array, $fromFactory->toArray());
        $this->assertEquals(PropertyType::LastEditedTime, $time->metadata()->type);
    }
}
