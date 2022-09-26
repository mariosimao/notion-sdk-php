<?php

namespace Notion\Test\Unit\Databases\Query;

use Notion\Databases\Query\NumberFilter;
use Notion\Databases\Query\Operator;
use PHPUnit\Framework\TestCase;

class NumberFilterTest extends TestCase
{
    public function test_empty_filter(): void
    {
        $filter = NumberFilter::property("Downloads");

        $this->assertSame("Downloads", $filter->propertyName());
        $this->assertSame(Operator::IsNotEmpty, $filter->operator());
        $this->assertTrue($filter->value());
    }

    public function test_equals(): void
    {
        $filter = NumberFilter::property("Downloads")->equals(1000);

        $expected = [
            "property" => "Downloads",
            "number" => [ "equals" => 1000 ],
        ];
        $this->assertSame($expected, $filter->toArray());
    }

    public function test_does_not_equal(): void
    {
        $filter = NumberFilter::property("Downloads")->doesNotEqual(1000);

        $expected = [
            "property" => "Downloads",
            "number" => [ "does_not_equal" => 1000 ],
        ];
        $this->assertSame($expected, $filter->toArray());
    }

    public function test_greater_than(): void
    {
        $filter = NumberFilter::property("Downloads")->greaterThan(1000);

        $expected = [
            "property" => "Downloads",
            "number" => [ "greater_than" => 1000 ],
        ];
        $this->assertSame($expected, $filter->toArray());
    }

    public function test_less_than(): void
    {
        $filter = NumberFilter::property("Downloads")->lessThan(1000);

        $expected = [
            "property" => "Downloads",
            "number" => [ "less_than" => 1000 ],
        ];
        $this->assertSame($expected, $filter->toArray());
    }

    public function test_greater_than_or_equal_to(): void
    {
        $filter = NumberFilter::property("Downloads")->greaterThanOrEqualTo(1000);

        $expected = [
            "property" => "Downloads",
            "number" => [ "greater_than_or_equal_to" => 1000 ],
        ];
        $this->assertSame($expected, $filter->toArray());
    }

    public function test_less_than_or_equal_to(): void
    {
        $filter = NumberFilter::property("Downloads")->lessThanOrEqualTo(1000);

        $expected = [
            "property" => "Downloads",
            "number" => [ "less_than_or_equal_to" => 1000 ],
        ];
        $this->assertSame($expected, $filter->toArray());
    }

    public function test_is_empty(): void
    {
        $filter = NumberFilter::property("Downloads")->isEmpty();

        $expected = [
            "property" => "Downloads",
            "number" => [ "is_empty" => true ],
        ];
        $this->assertSame($expected, $filter->toArray());
    }

    public function test_is_not_empty(): void
    {
        $filter = NumberFilter::property("Downloads")->isNotEmpty();

        $expected = [
            "property" => "Downloads",
            "number" => [ "is_not_empty" => true ],
        ];
        $this->assertSame($expected, $filter->toArray());
    }
}
