<?php

namespace Notion\Test\Unit\Databases\Properties;

use Notion\Databases\Properties\Factory;
use Notion\Databases\Properties\Formula;
use PHPUnit\Framework\TestCase;

class FormulaTest extends TestCase
{
    public function test_create(): void
    {
        $expression = "if(prop(\"In stock\"), 0, prop(\"Price\"))";
        $formula = Formula::create("Dummy prop name", $expression);

        $this->assertEquals("Dummy prop name", $formula->property()->name());
        $this->assertTrue($formula->property()->isFormula());
        $this->assertEquals($expression, $formula->expression());
    }

    public function test_change_expression(): void
    {
        $expression = "if(prop(\"In stock\"), 0, prop(\"Price\"))";
        $formula = Formula::create()->withExpression($expression);

        $this->assertEquals($expression, $formula->expression());
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
        $fromFactory = Factory::fromArray($array);

        $this->assertEquals($array, $formula->toArray());
        $this->assertEquals($array, $fromFactory->toArray());
    }
}
