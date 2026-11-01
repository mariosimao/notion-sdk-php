<?php

namespace Notion\Test\Unit\DataSources\Query;

use Notion\DataSources\Query\MultiSelectFilter;
use Notion\DataSources\Query\Operator;
use PHPUnit\Framework\TestCase;

class MultiSelectFilterTest extends TestCase
{
    public function test_empty_filter(): void
    {
        $filter = MultiSelectFilter::property("Categories");

        $this->assertSame("Categories", $filter->propertyName());
        $this->assertSame(Operator::IsNotEmpty, $filter->operator());
        $this->assertTrue($filter->value());
    }

    public function test_contains(): void
    {
        $filter = MultiSelectFilter::property("Categories")
            ->contains("Comedy");

        $this->assertSame("Categories", $filter->propertyName());
        $this->assertSame(Operator::Contains, $filter->operator());
        $this->assertSame("Comedy", $filter->value());

        $expected = [
            "property" => "Categories",
            "multi_select" => [ "contains" => "Comedy" ],
        ];
        $this->assertSame($expected, $filter->toArray());
    }

    public function test_does_not_contain(): void
    {
        $filter = MultiSelectFilter::property("Categories")
            ->doesNotContain("Comedy");

        $expected = [
            "property" => "Categories",
            "multi_select" => [ "does_not_contain" => "Comedy" ],
        ];
        $this->assertSame($expected, $filter->toArray());
    }

    public function test_is_empty(): void
    {
        $filter = MultiSelectFilter::property("Categories")->isEmpty();

        $expected = [
            "property" => "Categories",
            "multi_select" => [ "is_empty" => true ],
        ];
        $this->assertSame($expected, $filter->toArray());
    }

    public function test_is_not_empty(): void
    {
        $filter = MultiSelectFilter::property("Categories")->isNotEmpty();

        $expected = [
            "property" => "Categories",
            "multi_select" => [ "is_not_empty" => true ],
        ];
        $this->assertSame($expected, $filter->toArray());
    }

    public function test_contains_multiple_values(): void
    {
        $filter = MultiSelectFilter::property("Categories")->contains(["Marketing", "Sales"]);

        $expected = [
            "property" => "Categories",
            "multi_select" => [ "contains" => ["Marketing", "Sales"] ],
        ];
        $this->assertSame($expected, $filter->toArray());
        $this->assertSame(["Marketing", "Sales"], $filter->value());
    }

    public function test_does_not_contain_multiple_values(): void
    {
        $filter = MultiSelectFilter::property("Categories")->doesNotContain(["Engineering", "QA"]);

        $expected = [
            "property" => "Categories",
            "multi_select" => [ "does_not_contain" => ["Engineering", "QA"] ],
        ];
        $this->assertSame($expected, $filter->toArray());
        $this->assertSame(["Engineering", "QA"], $filter->value());
    }

    public function test_contains_single_value_list(): void
    {
        $filter = MultiSelectFilter::property("Categories")->contains(["Marketing"]);

        $expected = [
            "property" => "Categories",
            "multi_select" => [ "contains" => ["Marketing"] ],
        ];
        $this->assertSame($expected, $filter->toArray());
    }

    public function test_empty_list_is_rejected(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        MultiSelectFilter::property("Categories")->contains([]);
    }

    public function test_non_string_values_are_rejected(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        /** @psalm-suppress InvalidArgument */
        MultiSelectFilter::property("Categories")->doesNotContain(["Engineering", 1]);
    }
}
