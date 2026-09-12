<?php

namespace Notion\Test\Unit\Pages\PropertyItems;

use DateTimeImmutable;
use Notion\Common\Date;
use Notion\DataSources\Properties\RollupFunction;
use Notion\Pages\Properties\PropertyType;
use Notion\Pages\PropertyItems\RollupPropertyItem;
use Notion\Pages\PropertyItems\RollupType;
use PHPUnit\Framework\TestCase;

class RollupPropertyItemTest extends TestCase
{
    public function test_create_number(): void
    {
        $item = RollupPropertyItem::createNumber(RollupFunction::Count, 5, "prop-id");

        $this->assertTrue($item->isNumber());
        $this->assertFalse($item->isDate());
        $this->assertSame(5, $item->number);
        $this->assertSame(RollupFunction::Count, $item->function);
        $this->assertSame(RollupType::Number, $item->rollupType);
        $this->assertSame("prop-id", $item->metadata()->id);
        $this->assertSame(PropertyType::Rollup, $item->metadata()->type);
        $this->assertFalse($item->isEmpty());
    }

    public function test_create_date(): void
    {
        $date = Date::create(new DateTimeImmutable("2021-10-07T14:42:00.000Z"));
        $item = RollupPropertyItem::createDate(RollupFunction::LatestDate, $date);

        $this->assertTrue($item->isDate());
        $this->assertSame($date, $item->date);
        $this->assertSame(RollupFunction::LatestDate, $item->function);
        $this->assertSame(RollupType::Date, $item->rollupType);
        $this->assertFalse($item->isEmpty());
    }

    public function test_create_array(): void
    {
        $item = RollupPropertyItem::createArray(RollupFunction::ShowOriginal, [["foo" => "bar"]]);

        $this->assertTrue($item->isArray());
        $this->assertCount(1, $item->array);
        $this->assertSame(RollupFunction::ShowOriginal, $item->function);
        $this->assertSame(RollupType::Array, $item->rollupType);
        $this->assertFalse($item->isEmpty());
    }

    public function test_create_incomplete(): void
    {
        $item = RollupPropertyItem::createIncomplete(RollupFunction::Sum);

        $this->assertTrue($item->isIncomplete());
        $this->assertTrue($item->incomplete);
        $this->assertSame(RollupFunction::Sum, $item->function);
        $this->assertSame(RollupType::Incomplete, $item->rollupType);
        $this->assertTrue($item->isEmpty());
    }

    public function test_create_unsupported(): void
    {
        $item = RollupPropertyItem::createUnsupported(RollupFunction::Median);

        $this->assertTrue($item->isUnsupported());
        $this->assertTrue($item->unsupported);
        $this->assertSame(RollupFunction::Median, $item->function);
        $this->assertSame(RollupType::Unsupported, $item->rollupType);
        $this->assertTrue($item->isEmpty());
    }

    public function test_from_array_number(): void
    {
        $array = [
            "object" => "property_item",
            "id" => "rollup_prop",
            "type" => "rollup",
            "rollup" => [
                "type" => "number",
                "function" => "count",
                "number" => 42,
            ],
        ];

        $item = RollupPropertyItem::fromArray($array);

        $this->assertSame("rollup_prop", $item->metadata()->id);
        $this->assertTrue($item->isNumber());
        $this->assertSame(42, $item->number);
        $this->assertSame($array, $item->toArray());
    }

    public function test_empty_checks(): void
    {
        $numberEmpty = RollupPropertyItem::createNumber(RollupFunction::Count, null);
        $this->assertTrue($numberEmpty->isEmpty());

        $dateEmpty = RollupPropertyItem::createDate(RollupFunction::EarliestDate, null);
        $this->assertTrue($dateEmpty->isEmpty());

        $arrayEmpty = RollupPropertyItem::createArray(RollupFunction::ShowOriginal, []);
        $this->assertTrue($arrayEmpty->isEmpty());
    }

    public function test_to_array_for_various_types(): void
    {
        $date = Date::create(new DateTimeImmutable("2021-10-07T14:42:00.000Z"));
        $dateRollup = RollupPropertyItem::createDate(RollupFunction::LatestDate, $date);
        $this->assertEquals([
            "object" => "property_item",
            "id" => "",
            "type" => "rollup",
            "rollup" => [
                "type" => "date",
                "function" => "latest_date",
                "date" => $date->toArray(),
            ],
        ], $dateRollup->toArray());

        $arrayRollup = RollupPropertyItem::createArray(RollupFunction::ShowOriginal, [["item" => 1]]);
        $this->assertEquals([
            "object" => "property_item",
            "id" => "",
            "type" => "rollup",
            "rollup" => [
                "type" => "array",
                "function" => "show_original",
                "array" => [["item" => 1]],
            ],
        ], $arrayRollup->toArray());

        $incompleteRollup = RollupPropertyItem::createIncomplete(RollupFunction::Sum);
        $this->assertEquals([
            "object" => "property_item",
            "id" => "",
            "type" => "rollup",
            "rollup" => [
                "type" => "incomplete",
                "function" => "sum",
                "incomplete" => new \stdClass(),
            ],
        ], $incompleteRollup->toArray());

        $unsupportedRollup = RollupPropertyItem::createUnsupported(RollupFunction::Median);
        $this->assertEquals([
            "object" => "property_item",
            "id" => "",
            "type" => "rollup",
            "rollup" => [
                "type" => "unsupported",
                "function" => "median",
                "unsupported" => new \stdClass(),
            ],
        ], $unsupportedRollup->toArray());
    }
}
