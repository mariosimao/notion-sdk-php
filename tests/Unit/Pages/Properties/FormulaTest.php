<?php

namespace Notion\Test\Unit\Pages\Properties;

use DateTimeImmutable;
use Notion\Pages\Properties\PropertyFactory;
use Notion\Pages\Properties\Formula;
use Notion\Pages\Properties\FormulaType;
use Notion\Pages\Properties\PropertyType;
use PHPUnit\Framework\TestCase;

class FormulaTest extends TestCase
{
    public function test_string_from_array(): void
    {
        $array = [
            "id" => "a7ede3b7-c7ae-4eb8-b415-a7f80ac4dfe5",
            "type" => "formula",
            "formula" => [
                "type" => "string",
                "string" => "Formula result",
            ],
        ];

        $formula = Formula::fromArray($array);

        $this->assertEquals(PropertyType::Formula, $formula->metadata()->type);
        $this->assertEquals($array, $formula->toArray());
        $this->assertEquals($array, PropertyFactory::fromArray($array)->toArray());
        $this->assertEquals(FormulaType::String, $formula->type);
        $this->assertEquals("Formula result", $formula->string);
    }

    public function test_number_from_array(): void
    {
        $array = [
            "id" => "a7ede3b7-c7ae-4eb8-b415-a7f80ac4dfe5",
            "type" => "formula",
            "formula" => [
                "type" => "number",
                "number" => 123,
            ],
        ];

        $formula = Formula::fromArray($array);

        $this->assertEquals($array, $formula->toArray());
        $this->assertEquals($array, PropertyFactory::fromArray($array)->toArray());
        $this->assertEquals(FormulaType::Number, $formula->type);
        $this->assertEquals(123, $formula->number);
    }

    public function test_boolean_from_array(): void
    {
        $array = [
            "id" => "a7ede3b7-c7ae-4eb8-b415-a7f80ac4dfe5",
            "type" => "formula",
            "formula" => [
                "type" => "boolean",
                "boolean" => false,
            ],
        ];

        $formula = Formula::fromArray($array);

        $this->assertEquals($array, $formula->toArray());
        $this->assertEquals($array, PropertyFactory::fromArray($array)->toArray());
        $this->assertEquals(FormulaType::Boolean, $formula->type);
        $this->assertEquals(false, $formula->boolean);
    }

    public function test_date_from_array(): void
    {
        $array = [
            "id" => "a7ede3b7-c7ae-4eb8-b415-a7f80ac4dfe5",
            "type" => "formula",
            "formula" => [
                "type" => "date",
                "date" => [
                    "start" => "2021-01-01T00:00:00.000000Z",
                    "end" => null,
                ],
            ],
        ];

        $formula = Formula::fromArray($array);

        $this->assertEquals($array, $formula->toArray());
        $this->assertEquals($array, PropertyFactory::fromArray($array)->toArray());
        $this->assertTrue($formula->type === FormulaType::Date);
        $this->assertEquals(new DateTimeImmutable("2021-01-01T00:00:00.000000Z"), $formula->date?->start);
        $this->assertNull($formula->date?->end);
    }

    public function test_unsupported_from_array(): void
    {
        $array = [
            "id" => "a7ede3b7-c7ae-4eb8-b415-a7f80ac4dfe5",
            "type" => "formula",
            "formula" => [
                "type" => "unsupported",
                "unsupported" => new \stdClass(),
            ],
        ];

        $formula = Formula::fromArray($array);

        $this->assertEquals($array, $formula->toArray());
        $this->assertEquals($array, PropertyFactory::fromArray($array)->toArray());
        $this->assertSame(FormulaType::Unsupported, $formula->type);
        $this->assertTrue($formula->isUnsupported());
        $this->assertFalse($formula->isString());
        $this->assertFalse($formula->isNumber());
        $this->assertFalse($formula->isBoolean());
        $this->assertFalse($formula->isDate());
        $this->assertNull($formula->string);
        $this->assertNull($formula->number);
        $this->assertNull($formula->boolean);
        $this->assertNull($formula->date);
    }

    public function test_unsupported_from_array_with_empty_array(): void
    {
        $formula = Formula::fromArray([
            "id" => "abc",
            "type" => "formula",
            "formula" => [
                "type" => "unsupported",
                "unsupported" => [],
            ],
        ]);

        $this->assertTrue($formula->isUnsupported());
        $this->assertEquals([
            "id" => "abc",
            "type" => "formula",
            "formula" => [
                "type" => "unsupported",
                "unsupported" => new \stdClass(),
            ],
        ], $formula->toArray());
    }

    public function test_type_helpers(): void
    {
        $formula = Formula::fromArray([
            "id" => "abc",
            "type" => "formula",
            "formula" => [ "type" => "boolean", "boolean" => true ],
        ]);

        $this->assertTrue($formula->isBoolean());
        $this->assertFalse($formula->isUnsupported());
    }
}
