<?php

namespace Notion\Test\Unit\Pages\Properties;

use Notion\Pages\Properties\Factory;
use Notion\Pages\Properties\MultiSelect;
use Notion\Pages\Properties\Option;
use PHPUnit\Framework\TestCase;

class MultiSelectTest extends TestCase
{
    public function test_create_from_ids(): void
    {
        $id1 = "d69f85ae-9425-4851-beeb-4b4831f5786c";
        $id2 = "af65b5c5-8034-4ef9-91af-05b8bc01642e";

        $multiSelect = MultiSelect::fromIds($id1, $id2);

        $this->assertEquals($id1, $multiSelect->options()[0]->id());
        $this->assertEquals($id2, $multiSelect->options()[1]->id());

        $this->assertTrue($multiSelect->property()->isMultiSelect());
    }

    public function test_create_from_names(): void
    {
        $optionA = "Option A";
        $optionC = "Option C";

        $multiSelect = MultiSelect::fromNames($optionA, $optionC);

        $this->assertEquals($optionA, $multiSelect->options()[0]->name());
        $this->assertEquals($optionC, $multiSelect->options()[1]->name());
    }

    public function test_array_conversion(): void
    {
        $array = [
            "id" => "931db25b-f8af-4fc0-b7bf-eb9c29de6b87",
            "type" => "multi_select",
            "multi_select" => [
                [ "name" => "Option A", "color" => "red" ],
                [ "name" => "Option C", "color" => "blue" ],
            ],
        ];

        $multiSelect = MultiSelect::fromArray($array);
        $fromFactory = Factory::fromArray($array);

        $this->assertEquals($array, $multiSelect->toArray());
        $this->assertEquals($array, $fromFactory->toArray());
    }

    public function test_add_option(): void
    {
        $multiSelect = MultiSelect::fromNames("Option A", "Option B")
            ->addOption(Option::fromName("Option C"));

        $this->assertCount(3, $multiSelect->options());
    }
}
