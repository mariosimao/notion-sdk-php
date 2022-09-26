<?php

namespace Notion\Test\Unit\Databases\Query;

use Notion\Databases\Query\Operator;
use Notion\Databases\Query\SelectFilter;
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
}
