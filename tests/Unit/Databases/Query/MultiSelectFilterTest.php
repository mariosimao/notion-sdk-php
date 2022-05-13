<?php

namespace Notion\Test\Unit\Databases\Query;

use Notion\Databases\Query\MultiSelectFilter;
use PHPUnit\Framework\TestCase;

class MultiSelectFilterTest extends TestCase
{
    public function test_empty_filter(): void
    {
        $filter = MultiSelectFilter::property("Categories");

        $this->assertSame("property", $filter->propertyType());
        $this->assertSame("Categories", $filter->propertyName());
        $this->assertSame("is_not_empty", $filter->operator());
        $this->assertTrue($filter->value());
    }

    public function test_contains(): void
    {
        $filter = MultiSelectFilter::property("Categories")
            ->contains("Comedy");

        $this->assertSame("property", $filter->propertyType());
        $this->assertSame("Categories", $filter->propertyName());
        $this->assertSame("contains", $filter->operator());
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
}
