<?php

namespace Notion\Test\Unit\Pages\Properties;

use DateTimeImmutable;
use Notion\DataSources\Properties\RollupFunction;
use Notion\Pages\Properties\PropertyFactory;
use Notion\Pages\Properties\PropertyType;
use Notion\Pages\Properties\Rollup;
use Notion\Pages\Properties\RollupType;
use PHPUnit\Framework\TestCase;

class RollupTest extends TestCase
{
    public function test_number_from_array(): void
    {
        $array = [
            "id" => "abc",
            "type" => "rollup",
            "rollup" => [
                "type" => "number",
                "function" => "sum",
                "number" => 42,
            ],
        ];

        $rollup = Rollup::fromArray($array);

        $this->assertSame(PropertyType::Rollup, $rollup->metadata()->type);
        $this->assertSame(RollupType::Number, $rollup->type);
        $this->assertSame(RollupFunction::Sum, $rollup->function);
        $this->assertSame(42, $rollup->number);
        $this->assertTrue($rollup->isNumber());
        $this->assertEquals($array, $rollup->toArray());
        $this->assertEquals($array, PropertyFactory::fromArray($array)->toArray());
    }

    public function test_date_from_array(): void
    {
        $array = [
            "id" => "abc",
            "type" => "rollup",
            "rollup" => [
                "type" => "date",
                "function" => "latest_date",
                "date" => [
                    "start" => "2021-01-01T00:00:00.000000Z",
                    "end" => null,
                ],
            ],
        ];

        $rollup = Rollup::fromArray($array);

        $this->assertTrue($rollup->isDate());
        $this->assertEquals(new DateTimeImmutable("2021-01-01T00:00:00.000000Z"), $rollup->date?->start);
        $this->assertEquals($array, $rollup->toArray());
    }

    public function test_array_from_array(): void
    {
        $array = [
            "id" => "abc",
            "type" => "rollup",
            "rollup" => [
                "type" => "array",
                "function" => "show_original",
                "array" => [
                    [ "type" => "number", "number" => 1 ],
                    [ "type" => "number", "number" => 2 ],
                ],
            ],
        ];

        $rollup = Rollup::fromArray($array);

        $this->assertTrue($rollup->isArray());
        $this->assertCount(2, $rollup->array);
        $this->assertEquals($array, $rollup->toArray());
    }

    public function test_incomplete_from_array(): void
    {
        $array = [
            "id" => "abc",
            "type" => "rollup",
            "rollup" => [
                "type" => "incomplete",
                "function" => "count",
                "incomplete" => new \stdClass(),
            ],
        ];

        $rollup = Rollup::fromArray($array);

        $this->assertTrue($rollup->isIncomplete());
        $this->assertEquals($array, $rollup->toArray());
    }

    public function test_unsupported_from_array(): void
    {
        $array = [
            "id" => "abc",
            "type" => "rollup",
            "rollup" => [
                "type" => "unsupported",
                "function" => "sum",
                "unsupported" => new \stdClass(),
            ],
        ];

        $rollup = Rollup::fromArray($array);

        $this->assertSame(RollupType::Unsupported, $rollup->type);
        $this->assertSame(RollupFunction::Sum, $rollup->function);
        $this->assertTrue($rollup->isUnsupported());
        $this->assertFalse($rollup->isNumber());
        $this->assertFalse($rollup->isDate());
        $this->assertFalse($rollup->isArray());
        $this->assertFalse($rollup->isIncomplete());
        $this->assertNull($rollup->number);
        $this->assertNull($rollup->date);
        $this->assertSame([], $rollup->array);
        $this->assertEquals($array, $rollup->toArray());
        $this->assertInstanceOf(Rollup::class, PropertyFactory::fromArray($array));
        $this->assertEquals($array, PropertyFactory::fromArray($array)->toArray());
    }

    public function test_unsupported_from_array_with_empty_array(): void
    {
        $rollup = Rollup::fromArray([
            "id" => "abc",
            "type" => "rollup",
            "rollup" => [
                "type" => "unsupported",
                "function" => "count",
                "unsupported" => [],
            ],
        ]);

        $this->assertTrue($rollup->isUnsupported());
        $this->assertSame(RollupFunction::Count, $rollup->function);
    }
}
