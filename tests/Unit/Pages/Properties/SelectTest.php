<?php

namespace Notion\Test\Unit\Pages\Properties;

use Notion\Common\Color;
use Notion\Databases\Properties\SelectOption;
use Notion\Pages\Properties\PropertyFactory;
use Notion\Pages\Properties\PropertyType;
use Notion\Pages\Properties\Select;
use PHPUnit\Framework\TestCase;

class SelectTest extends TestCase
{
    public function test_create_from_option_id(): void
    {
        $select = Select::fromId("e69017d3-9027-46c4-9b6f-490d243e459b");

        $this->assertEquals("e69017d3-9027-46c4-9b6f-490d243e459b", $select->option?->id);
        $this->assertNull($select->option?->name);
        $this->assertEquals("", $select->metadata()->id);
        $this->assertTrue($select->metadata()->type === PropertyType::Select);
    }

    public function test_create_from_option_name(): void
    {
        $select = Select::fromName("Option A");

        $this->assertEquals("Option A", $select->option?->name);
        $this->assertNull($select->option?->id);
        $this->assertEquals("", $select->metadata()->id);
        $this->assertTrue($select->metadata()->type === PropertyType::Select);
    }

    public function test_create_from_option(): void
    {
        $option = SelectOption::fromId("abc");

        $select = Select::fromOption($option);

        $this->assertEquals("abc", $select->option?->id);
    }

    public function test_create_empty(): void
    {
        $select = Select::createEmpty();

        $this->assertTrue($select->isEmpty());
    }

    public function test_change_option(): void
    {
        $optionA = SelectOption::fromId("abc");
        $optionB = SelectOption::fromId("def");

        $select = Select::fromOption($optionA);
        $select = $select->changeOption($optionB);

        $this->assertEquals($optionB, $select->option);
    }

    public function test_clear(): void
    {
        $option = SelectOption::fromId("abc");

        $select = Select::fromOption($option)->clear();

        $this->assertTrue($select->isEmpty());
    }

    public function test_array_conversion(): void
    {
        $array = [
            "id"     => "a7ede3b7-c7ae-4eb8-b415-a7f80ac4dfe5",
            "type"   => "select",
            "select" => [
                "name"  => "Option A",
                "id"    => "ad762674-9280-444b-96a7-3a0fb0aefff9",
                "color" => "default",
            ],
        ];

        $select = Select::fromArray($array);
        $fromFactory = PropertyFactory::fromArray($array);

        $this->assertEquals($array, $select->toArray());
        $this->assertEquals($array, $fromFactory->toArray());
    }

    public function test_is_empty(): void
    {
        $array = [
            "id"     => "a7ede3b7-c7ae-4eb8-b415-a7f80ac4dfe5",
            "type"   => "select",
            "select" => null,
        ];

        $select = Select::fromArray($array);

        $this->assertTrue($select->isEmpty());
    }
}
