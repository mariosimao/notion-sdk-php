<?php

namespace Notion\Test\Unit\Pages\PropertyItems;

use DateTimeImmutable;
use Notion\Common\Date;
use Notion\Pages\Properties\FormulaType;
use Notion\Pages\Properties\PropertyType;
use Notion\Pages\PropertyItems\FormulaPropertyItem;
use PHPUnit\Framework\TestCase;

class FormulaPropertyItemTest extends TestCase
{
    public function test_create_number(): void
    {
        $item = FormulaPropertyItem::createNumber(1234, "formula-id");

        $this->assertSame("formula-id", $item->metadata()->id);
        $this->assertSame(PropertyType::Formula, $item->metadata()->type);
        $this->assertTrue($item->isNumber());
        $this->assertSame(1234, $item->number);
        $this->assertFalse($item->isString());
    }

    public function test_create_string(): void
    {
        $item = FormulaPropertyItem::createString("result");

        $this->assertTrue($item->isString());
        $this->assertSame("result", $item->string);
    }

    public function test_create_boolean(): void
    {
        $item = FormulaPropertyItem::createBoolean(true);

        $this->assertTrue($item->isBoolean());
        $this->assertTrue($item->boolean);
    }

    public function test_create_date(): void
    {
        $date = Date::create(new DateTimeImmutable("2021-05-11T11:00:00.000Z"));
        $item = FormulaPropertyItem::createDate($date);

        $this->assertTrue($item->isDate());
        $this->assertSame($date, $item->date);
    }

    public function test_create_unsupported(): void
    {
        $item = FormulaPropertyItem::createUnsupported();

        $this->assertTrue($item->isUnsupported());
        $this->assertTrue($item->unsupported);
    }

    public function test_from_array_number(): void
    {
        $array = [
            "object" => "property_item",
            "id" => "KpQq",
            "type" => "formula",
            "formula" => [
                "type" => "number",
                "number" => 1234,
            ],
        ];

        $item = FormulaPropertyItem::fromArray($array);

        $this->assertSame("KpQq", $item->metadata()->id);
        $this->assertTrue($item->isNumber());
        $this->assertSame(1234, $item->number);
        $this->assertSame($array, $item->toArray());
    }

    public function test_from_array_unsupported(): void
    {
        $array = [
            "object" => "property_item",
            "id" => "KpQq",
            "type" => "formula",
            "formula" => [
                "type" => "unsupported",
                "unsupported" => new \stdClass(),
            ],
        ];

        $item = FormulaPropertyItem::fromArray($array);

        $this->assertTrue($item->isUnsupported());
        $this->assertEquals($array, $item->toArray());
    }
}
