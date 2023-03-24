<?php

namespace Notion\Test\Unit\Databases\Query;

use Notion\Databases\Query\Operator;
use Notion\Databases\Query\RelationFilter;
use PHPUnit\Framework\TestCase;

class RelationFilterTest extends TestCase
{
    public function test_empty_filter(): void
    {
        $filter = RelationFilter::property("Category");

        $this->assertSame("Category", $filter->propertyName());
        $this->assertSame(Operator::IsNotEmpty, $filter->operator());
        $this->assertTrue($filter->value());
    }

    public function test_contains(): void
    {
        $filter = RelationFilter::property("Category")->contains("Blog");

        $expected = [
            "property" => "Category",
            "relation" => [ "contains" => "Blog" ],
        ];
        $this->assertSame($expected, $filter->toArray());
    }

    public function test_does_not_contain(): void
    {
        $filter = RelationFilter::property("Category")->doesNotContain("Blog");

        $expected = [
            "property" => "Category",
            "relation" => [ "does_not_contain" => "Blog" ],
        ];
        $this->assertSame($expected, $filter->toArray());
    }

    public function test_is_empty(): void
    {
        $filter = RelationFilter::property("Category")->isEmpty();

        $expected = [
            "property" => "Category",
            "relation" => [ "is_empty" => true ],
        ];
        $this->assertSame($expected, $filter->toArray());
    }

    public function test_is_not_empty(): void
    {
        $filter = RelationFilter::property("Category")->isNotEmpty();

        $expected = [
            "property" => "Category",
            "relation" => [ "is_not_empty" => true ],
        ];
        $this->assertSame($expected, $filter->toArray());
    }
}
