<?php

namespace Notion\Test\Unit\Databases\Properties;

use Notion\Databases\Properties\Factory;
use Notion\Databases\Properties\MultiSelect;
use Notion\Databases\Properties\SelectOption;
use PHPUnit\Framework\TestCase;

class MultiSelectTest extends TestCase
{
    public function test_create(): void
    {
        $select = MultiSelect::create("Dummy prop name");

        $this->assertEquals("Dummy prop name", $select->property()->name());
        $this->assertTrue($select->property()->isMultiSelect());
        $this->assertEmpty($select->options());
    }

    public function test_replace_options(): void
    {
        $select = MultiSelect::create()->withOptions([
            SelectOption::create("Option A"),
            SelectOption::create("Option B"),
        ]);

        $this->assertCount(2, $select->options());
    }

    public function test_add_option(): void
    {
        $option = SelectOption::create("Option A");
        $select = MultiSelect::create()->addOption($option);

        $this->assertEquals([ $option ], $select->options());
    }

    public function test_array_conversion(): void
    {
        $array = [
            "id"    => "abc",
            "name"  => "MultiSelect",
            "type"  => "multi_select",
            "multi_select" => [
                "options" => [
                    [ "id" => "aaa", "name" => "Option A", "color" => "default" ],
                    [ "id" => "bbb", "name" => "Option B", "color" => "default" ],
                ],
            ],
        ];
        $select = MultiSelect::fromArray($array);
        $fromFactory = Factory::fromArray($array);

        $this->assertEquals($array, $select->toArray());
        $this->assertEquals($array, $fromFactory->toArray());
    }
}
