<?php

namespace Notion\Test\Unit\DataSources\Query;

use Notion\DataSources\Query\Operator;
use Notion\DataSources\Query\SelectFilter;
use PHPUnit\Framework\TestCase;

class SelectFilterTest extends TestCase
{
    public function test_empty_filter(): void
    {
        $filter = SelectFilter::property("Category");

        $this->assertSame("Category", $filter->propertyName());
        $this->assertSame(Operator::IsNotEmpty, $filter->operator());
        $this->assertTrue($filter->value());
    }

    public function test_equals(): void
    {
        $filter = SelectFilter::property("Category")->equals("Comedy");

        $expected = [
            "property" => "Category",
            "select" => [ "equals" => "Comedy" ],
        ];
        $this->assertSame($expected, $filter->toArray());
    }

    public function test_does_not_equal(): void
    {
        $filter = SelectFilter::property("Category")->doesNotEqual("Comedy");

        $expected = [
            "property" => "Category",
            "select" => [ "does_not_equal" => "Comedy" ],
        ];
        $this->assertSame($expected, $filter->toArray());
    }

    public function test_is_empty(): void
    {
        $filter = SelectFilter::property("Category")->isEmpty();

        $expected = [
            "property" => "Category",
            "select" => [ "is_empty" => true ],
        ];
        $this->assertSame($expected, $filter->toArray());
    }

    public function test_is_not_empty(): void
    {
        $filter = SelectFilter::property("Category")->isNotEmpty();

        $expected = [
            "property" => "Category",
            "select" => [ "is_not_empty" => true ],
        ];
        $this->assertSame($expected, $filter->toArray());
    }

    public function test_equals_multiple_values(): void
    {
        $filter = SelectFilter::property("Category")->equals(["Comedy", "Drama"]);

        $expected = [
            "property" => "Category",
            "select" => [ "equals" => ["Comedy", "Drama"] ],
        ];
        $this->assertSame($expected, $filter->toArray());
        $this->assertSame(["Comedy", "Drama"], $filter->value());
    }

    public function test_does_not_equal_multiple_values(): void
    {
        $filter = SelectFilter::property("Category")->doesNotEqual(["Comedy", "Drama"]);

        $expected = [
            "property" => "Category",
            "select" => [ "does_not_equal" => ["Comedy", "Drama"] ],
        ];
        $this->assertSame($expected, $filter->toArray());
        $this->assertSame(["Comedy", "Drama"], $filter->value());
    }

    public function test_equals_single_value_list(): void
    {
        $filter = SelectFilter::property("Category")->equals(["Comedy"]);

        $expected = [
            "property" => "Category",
            "select" => [ "equals" => ["Comedy"] ],
        ];
        $this->assertSame($expected, $filter->toArray());
    }

    public function test_empty_list_is_rejected(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        SelectFilter::property("Category")->equals([]);
    }

    public function test_non_string_values_are_rejected(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        /** @psalm-suppress InvalidArgument */
        SelectFilter::property("Category")->doesNotEqual(["Comedy", 1]);
    }

    public function test_non_list_array_is_rejected(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        /** @psalm-suppress ArgumentTypeCoercion */
        SelectFilter::property("Category")->equals(["a" => "Comedy"]);
    }
}
