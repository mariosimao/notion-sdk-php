<?php

namespace Notion\Test\Unit\Pages\Properties;

use Notion\Pages\Properties\Factory;
use Notion\Pages\Properties\Select;
use PHPUnit\Framework\TestCase;

class SelectTest extends TestCase
{
    public function test_create_from_option_id(): void
    {
        $select = Select::fromId("e69017d3-9027-46c4-9b6f-490d243e459b");

        $this->assertEquals("e69017d3-9027-46c4-9b6f-490d243e459b", $select->id());
        $this->assertNull($select->name());
        $this->assertEquals("", $select->property()->id());
        $this->assertEquals("select", $select->property()->type());
        $this->assertTrue($select->property()->isSelect());
    }

    public function test_create_from_option_name(): void
    {
        $select = Select::fromName("Option A");

        $this->assertEquals("Option A", $select->name());
        $this->assertNull($select->id());
        $this->assertEquals("", $select->property()->id());
        $this->assertEquals("select", $select->property()->type());
        $this->assertTrue($select->property()->isSelect());
    }

    public function test_array_conversion(): void
    {
        $array = [
            "id"     => "a7ede3b7-c7ae-4eb8-b415-a7f80ac4dfe5",
            "type"   => "select",
            "select" => [
                "name" => "Option A",
                "id"   => "ad762674-9280-444b-96a7-3a0fb0aefff9",
            ],
        ];

        $select = Select::fromArray($array);
        $fromFactory = Factory::fromArray($array);

        $this->assertEquals($array, $select->toArray());
        $this->assertEquals($array, $fromFactory->toArray());
    }

    public function test_change_option_name(): void
    {
        $select = Select::fromName("Option A")->withName("Option B");
        $this->assertEquals("Option B", $select->name());
    }

    public function test_change_option_id(): void
    {
        $select = Select::fromId("ad3d06a7-4245-4c71-9bf4-f285b251f92e")
            ->withId("ad762674-9280-444b-96a7-3a0fb0aefff9");

        $this->assertEquals("ad762674-9280-444b-96a7-3a0fb0aefff9", $select->id());
    }
}
