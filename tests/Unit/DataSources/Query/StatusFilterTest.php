<?php

namespace Notion\Test\Unit\DataSources\Query;

use Notion\DataSources\Query\Operator;
use Notion\DataSources\Query\StatusFilter;
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

    public function test_equals_multiple_values(): void
    {
        $filter = StatusFilter::property("Status")->equals(["Not started", "In progress"]);

        $expected = [
            "property" => "Status",
            "status" => [ "equals" => ["Not started", "In progress"] ],
        ];
        $this->assertSame($expected, $filter->toArray());
        $this->assertSame(["Not started", "In progress"], $filter->value());
    }

    public function test_does_not_equal_multiple_values(): void
    {
        $filter = StatusFilter::property("Status")->doesNotEqual(["Done", "Archive"]);

        $expected = [
            "property" => "Status",
            "status" => [ "does_not_equal" => ["Done", "Archive"] ],
        ];
        $this->assertSame($expected, $filter->toArray());
        $this->assertSame(["Done", "Archive"], $filter->value());
    }

    public function test_equals_single_value_list(): void
    {
        $filter = StatusFilter::property("Status")->equals(["Not started"]);

        $expected = [
            "property" => "Status",
            "status" => [ "equals" => ["Not started"] ],
        ];
        $this->assertSame($expected, $filter->toArray());
    }

    public function test_empty_list_is_rejected(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        StatusFilter::property("Status")->equals([]);
    }

    public function test_non_string_values_are_rejected(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        /** @psalm-suppress InvalidArgument */
        StatusFilter::property("Status")->doesNotEqual(["Done", 1]);
    }
}
