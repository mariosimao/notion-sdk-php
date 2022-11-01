<?php

namespace Notion\Test\Unit\Common;

use Notion\Common\Equation;
use PHPUnit\Framework\TestCase;

class EquationTest extends TestCase
{
    public function test_array_conversion(): void
    {
        $array = [
            "expression" => "a^2 + b^2 = c^2",
        ];

        $equation = Equation::fromArray($array);

        $this->assertEquals($array, $equation->toArray());
    }

    public function test_create_from_expression(): void
    {
        $equation = Equation::fromString("a^2 + b^2 = c^2");

        $this->assertEquals("a^2 + b^2 = c^2", $equation->expression);
    }
}
