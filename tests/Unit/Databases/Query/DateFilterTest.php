<?php

namespace Notion\Test\Unit\Databases\Query;

use Notion\Databases\Query\DateFilter;
use Notion\Databases\Query\Operator;
use PHPUnit\Framework\TestCase;
use stdClass;

class DateFilterTest extends TestCase
{
    public function test_property(): void
    {
        $filter = DateFilter::property("Release date");

        $this->assertSame("property", $filter->propertyType());
        $this->assertSame("Release date", $filter->propertyName());
        $this->assertSame(Operator::IsNotEmpty, $filter->operator());
        $this->assertTrue($filter->value());
    }

    public function test_created_time(): void
    {
        $filter = DateFilter::createdTime();

        $this->assertSame("timestamp", $filter->propertyType());
        $this->assertSame("created_time", $filter->propertyName());
        $this->assertSame(Operator::IsNotEmpty, $filter->operator());
        $this->assertTrue($filter->value());
    }

    public function test_last_edited_Time(): void
    {
        $filter = DateFilter::lastEditedTime();

        $this->assertSame("timestamp", $filter->propertyType());
        $this->assertSame("last_edited_time", $filter->propertyName());
        $this->assertSame(Operator::IsNotEmpty, $filter->operator());
        $this->assertTrue($filter->value());
    }

    public function test_equals(): void
    {
        $filter = DateFilter::createdTime()
            ->equals("2022-02-13");

        $expected = [
            "timestamp" => "created_time",
            "created_time" => [ "equals" => "2022-02-13" ],
        ];
        $this->assertSame($expected, $filter->toArray());
    }

    public function test_before(): void
    {
        $filter = DateFilter::createdTime()
            ->before("2021-05-10T12:00:00");

        $expected = [
            "timestamp" => "created_time",
            "created_time" => [ "before" => "2021-05-10T12:00:00" ],
        ];
        $this->assertSame($expected, $filter->toArray());
    }

    public function test_after(): void
    {
        $filter = DateFilter::createdTime()
            ->after("2021-05-10T12:00:00");

        $expected = [
            "timestamp" => "created_time",
            "created_time" => [ "after" => "2021-05-10T12:00:00" ],
        ];
        $this->assertSame($expected, $filter->toArray());
    }

    public function test_on_or_before(): void
    {
        $filter = DateFilter::property("Release date")
            ->onOrBefore("1997-12-27");

        $expected = [
            "property" => "Release date",
            "date" => [ "on_or_before" => "1997-12-27" ],
        ];
        $this->assertSame($expected, $filter->toArray());
    }

    public function test_is_empty(): void
    {
        $filter = DateFilter::property("Release date")
            ->isEmpty();

        $expected = [
            "property" => "Release date",
            "date" => [ "is_empty" => true ],
        ];
        $this->assertSame($expected, $filter->toArray());
    }

    public function test_is_not_empty(): void
    {
        $filter = DateFilter::property("Release date")
            ->isNotEmpty();

        $expected = [
            "property" => "Release date",
            "date" => [ "is_not_empty" => true ],
        ];
        $this->assertSame($expected, $filter->toArray());
    }

    public function test_on_or_after(): void
    {
        $filter = DateFilter::property("Release date")
            ->onOrAfter("1997-12-27");

        $expected = [
            "property" => "Release date",
            "date" => [ "on_or_after" => "1997-12-27" ],
        ];
        $this->assertSame($expected, $filter->toArray());
    }

    public function test_past_week(): void
    {
        $filter = DateFilter::property("Release date")
            ->pastWeek();

        $expected = [
            "property" => "Release date",
            "date" => [ "past_week" => new stdClass() ],
        ];
        $this->assertEquals($expected, $filter->toArray());
    }

    public function test_past_month(): void
    {
        $filter = DateFilter::property("Release date")
            ->pastMonth();

        $expected = [
            "property" => "Release date",
            "date" => [ "past_month" => new stdClass() ],
        ];
        $this->assertEquals($expected, $filter->toArray());
    }

    public function test_past_year(): void
    {
        $filter = DateFilter::property("Release date")
            ->pastYear();

        $expected = [
            "property" => "Release date",
            "date" => [ "past_year" => new stdClass() ],
        ];
        $this->assertEquals($expected, $filter->toArray());
    }

    public function test_next_week(): void
    {
        $filter = DateFilter::property("Release date")
            ->nextWeek();

        $expected = [
            "property" => "Release date",
            "date" => [ "next_week" => new stdClass() ],
        ];
        $this->assertEquals($expected, $filter->toArray());
    }

    public function test_next_month(): void
    {
        $filter = DateFilter::property("Release date")
            ->nextMonth();

        $expected = [
            "property" => "Release date",
            "date" => [ "next_month" => new stdClass() ],
        ];
        $this->assertEquals($expected, $filter->toArray());
    }

    public function test_next_year(): void
    {
        $filter = DateFilter::property("Release date")
            ->nextYear();

        $expected = [
            "property" => "Release date",
            "date" => [ "next_year" => new stdClass() ],
        ];
        $this->assertEquals($expected, $filter->toArray());
    }

    public function test_this_week(): void
    {
        $filter = DateFilter::property("Release date")
            ->thisWeek();

        $expected = [
            "property" => "Release date",
            "date" => [ "this_week" => new stdClass() ],
        ];
        $this->assertEquals($expected, $filter->toArray());
    }
}
