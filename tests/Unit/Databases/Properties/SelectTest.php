<?php

namespace Notion\Test\Unit\Databases\Properties;

use Notion\Databases\Properties\PropertyFactory;
use Notion\Databases\Properties\PropertyType;
use Notion\Databases\Properties\Select;
use Notion\Databases\Properties\SelectOption;
use PHPUnit\Framework\TestCase;

class SelectTest extends TestCase
{
    public function test_create(): void
    {
        $select = Select::create("Dummy prop name");

        $this->assertEquals("Dummy prop name", $select->metadata()->name);
        $this->assertEquals(PropertyType::Select, $select->metadata()->type);
        $this->assertEmpty($select->options);
    }

    public function test_replace_options(): void
    {
        $select = Select::create()->changeOptions(
            SelectOption::fromName("Option A"),
            SelectOption::fromName("Option B"),
        );

        $this->assertCount(2, $select->options);
    }

    public function test_add_option(): void
    {
        $option = SelectOption::fromName("Option A");
        $select = Select::create()->addOption($option);

        $this->assertEquals([ $option ], $select->options);
    }

    public function test_array_conversion(): void
    {
        $array = [
            "id"    => "abc",
            "name"  => "Select",
            "type"  => "select",
            "select" => [
                "options" => [
                    [ "id" => "aaa", "name" => "Option A", "color" => "default" ],
                    [ "id" => "bbb", "name" => "Option B", "color" => "default" ],
                ],
            ],
        ];
        $select = Select::fromArray($array);
        $fromFactory = PropertyFactory::fromArray($array);

        $this->assertEquals($array, $select->toArray());
        $this->assertEquals($array, $fromFactory->toArray());
    }
}
