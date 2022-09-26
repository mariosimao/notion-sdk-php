<?php

namespace Notion\Test\Unit\Databases\Properties;

use Notion\Databases\Properties\PropertyFactory;
use Notion\Databases\Properties\Formula;
use Notion\Databases\Properties\PropertyType;
use PHPUnit\Framework\TestCase;

class FormulaTest extends TestCase
{
    public function test_create(): void
    {
        $expression = "if(prop(\"In stock\"), 0, prop(\"Price\"))";
        $formula = Formula::create("Dummy prop name", $expression);

        $this->assertEquals("Dummy prop name", $formula->metadata()->name);
        $this->assertEquals(PropertyType::Formula, $formula->metadata()->type);
        $this->assertEquals($expression, $formula->expression);
    }

    public function test_change_expression(): void
    {
        $expression = "if(prop(\"In stock\"), 0, prop(\"Price\"))";
        $formula = Formula::create()->changeExpression($expression);

        $this->assertEquals($expression, $formula->expression);
    }

    public function test_array_conversion(): void
    {
        $array = [
            "id"    => "abc",
            "name"  => "dummy",
            "type"  => "formula",
            "formula" => [
                "expression" => "if(prop(\"In stock\"), 0, prop(\"Price\"))",
            ],
        ];
        $formula = Formula::fromArray($array);
        $fromFactory = PropertyFactory::fromArray($array);

        $this->assertEquals($array, $formula->toArray());
        $this->assertEquals($array, $fromFactory->toArray());
    }
}
