<?php

namespace Notion\Test\Unit\Databases\Query;

use Notion\Databases\Query\Operator;
use Notion\Databases\Query\StatusFilter;
use PHPUnit\Framework\TestCase;

class StatusFilterTest extends TestCase
{
    public function test_empty_filter(): void
    {
        $filter = StatusFilter::property("Status");

        $this->assertSame("Status", $filter->propertyName());
        $this->assertSame(Operator::IsNotEmpty, $filter->operator());
        $this->assertTrue($filter->value());
    }

    public function test_equals(): void
    {
        $filter = StatusFilter::property("Status")->equals("Completed");

        $expected = [
            "property" => "Status",
            "status" => [ "equals" => "Completed" ],
        ];
        $this->assertSame($expected, $filter->toArray());
    }

    public function test_does_not_equal(): void
    {
        $filter = StatusFilter::property("Status")->doesNotEqual("In Progress");

        $expected = [
            "property" => "Status",
            "status" => [ "does_not_equal" => "In Progress" ],
        ];
        $this->assertSame($expected, $filter->toArray());
    }

    public function test_is_empty(): void
    {
        $filter = StatusFilter::property("Status")->isEmpty();

        $expected = [
            "property" => "Status",
            "status" => [ "is_empty" => true ],
        ];
        $this->assertSame($expected, $filter->toArray());
    }

    public function test_is_not_empty(): void
    {
        $filter = StatusFilter::property("Status")->isNotEmpty();

        $expected = [
            "property" => "Status",
            "status" => [ "is_not_empty" => true ],
        ];
        $this->assertSame($expected, $filter->toArray());
    }
}
