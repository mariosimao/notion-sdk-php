<?php

namespace Notion\Test\Unit\Views\Configuration;

use Notion\Exceptions\ViewException;
use Notion\Views\Configuration\DateGroupBy;
use Notion\Views\Configuration\GroupBy;
use Notion\Views\Configuration\GroupByType;
use Notion\Views\Configuration\GroupSort;
use Notion\Views\Configuration\StatusGroupBy;
use PHPUnit\Framework\TestCase;

class GroupByTest extends TestCase
{
    public function test_select(): void
    {
        $groupBy = GroupBy::select("abc")->changeSort(GroupSort::Ascending)->hideEmptyGroups();

        $this->assertSame([
            "type"              => "select",
            "property_id"       => "abc",
            "sort"              => [ "type" => "ascending" ],
            "hide_empty_groups" => true,
        ], $groupBy->toArray());
    }

    public function test_status(): void
    {
        $groupBy = GroupBy::status("abc", StatusGroupBy::Option)->showEmptyGroups();

        $this->assertSame([
            "type"              => "status",
            "property_id"       => "abc",
            "group_by"          => "option",
            "sort"              => [ "type" => "manual" ],
            "hide_empty_groups" => false,
        ], $groupBy->toArray());
    }

    public function test_date_with_start_day_of_week(): void
    {
        $groupBy = GroupBy::date("abc", DateGroupBy::Week)->changeStartDayOfWeek(1);

        $this->assertSame([
            "type"              => "date",
            "property_id"       => "abc",
            "group_by"          => "week",
            "sort"              => [ "type" => "manual" ],
            "start_day_of_week" => 1,
        ], $groupBy->toArray());
    }

    public function test_invalid_start_day_of_week(): void
    {
        $this->expectException(ViewException::class);
        GroupBy::date("abc", DateGroupBy::Week)->changeStartDayOfWeek(3);
    }

    public function test_start_day_of_week_not_supported(): void
    {
        $this->expectException(ViewException::class);
        GroupBy::select("abc")->changeStartDayOfWeek(0);
    }

    public function test_number_range(): void
    {
        $groupBy = GroupBy::number("abc")->changeNumberRange(0, 100, 10);

        $array = $groupBy->toArray();
        $this->assertSame(0, $array["range_start"]);
        $this->assertSame(100, $array["range_end"]);
        $this->assertSame(10, $array["range_size"]);
    }

    public function test_number_range_not_supported(): void
    {
        $this->expectException(ViewException::class);
        GroupBy::checkbox("abc")->changeNumberRange(0, 100, 10);
    }

    public function test_formula(): void
    {
        $groupBy = GroupBy::formula("abc", GroupBy::formulaDateResult(DateGroupBy::Month));

        $this->assertSame([
            "type"        => "formula",
            "property_id" => "abc",
            "group_by"    => [
                "type"     => "date",
                "group_by" => "month",
                "sort"     => [ "type" => "manual" ],
            ],
        ], $groupBy->toArray());
    }

    public function test_from_array(): void
    {
        $array = [
            "type"          => "formula",
            "property_id"   => "abc",
            "property_name" => "Formula",
            "group_by"      => [ "type" => "text", "group_by" => "alphabet_prefix" ],
        ];

        $groupBy = GroupBy::fromArray($array);

        $this->assertSame(GroupByType::Formula, $groupBy->type);
        $this->assertSame("Formula", $groupBy->propertyName);
        $this->assertInstanceOf(GroupBy::class, $groupBy->groupBy);
        $this->assertSame(GroupByType::Text, $groupBy->groupBy->type);
        $this->assertSame([
            "type"        => "formula",
            "property_id" => "abc",
            "group_by"    => [ "type" => "text", "group_by" => "alphabet_prefix" ],
        ], $groupBy->toArray());
    }
}
