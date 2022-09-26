<?php

namespace Notion\Test\Unit\Databases\Query;

use Notion\Databases\Query\Operator;
use Notion\Databases\Query\PeopleFilter;
use PHPUnit\Framework\TestCase;

class PeopleFilterTest extends TestCase
{
    public function test_empty_filter(): void
    {
        $filter = PeopleFilter::property("Friends");

        $this->assertSame("Friends", $filter->propertyName());
        $this->assertSame(Operator::IsNotEmpty, $filter->operator());
        $this->assertTrue($filter->value());
    }

    public function test_created_by(): void
    {
        $filter = PeopleFilter::createdBy();

        $this->assertSame("created_by", $filter->propertyName());
    }

    public function test_last_edited_by(): void
    {
        $filter = PeopleFilter::lastEditedBy();

        $this->assertSame("last_edited_by", $filter->propertyName());
    }

    public function test_contains(): void
    {
        $filter = PeopleFilter::property("Friends")
            ->contains("7b23ad4e145c41aea5604374406c2bc0");

        $this->assertSame("Friends", $filter->propertyName());
        $this->assertSame(Operator::Contains, $filter->operator());
        $this->assertSame("7b23ad4e145c41aea5604374406c2bc0", $filter->value());

        $expected = [
            "property" => "Friends",
            "people" => [ "contains" => "7b23ad4e145c41aea5604374406c2bc0" ],
        ];
        $this->assertSame($expected, $filter->toArray());
    }

    public function test_does_not_contain(): void
    {
        $filter = PeopleFilter::property("Friends")
            ->doesNotContain("7b23ad4e145c41aea5604374406c2bc0");

        $expected = [
            "property" => "Friends",
            "people" => [ "does_not_contain" => "7b23ad4e145c41aea5604374406c2bc0" ],
        ];
        $this->assertSame($expected, $filter->toArray());
    }

    public function test_is_empty(): void
    {
        $filter = PeopleFilter::property("Friends")->isEmpty();

        $expected = [
            "property" => "Friends",
            "people" => [ "is_empty" => true ],
        ];
        $this->assertSame($expected, $filter->toArray());
    }

    public function test_is_not_empty(): void
    {
        $filter = PeopleFilter::property("Friends")->isNotEmpty();

        $expected = [
            "property" => "Friends",
            "people" => [ "is_not_empty" => true ],
        ];
        $this->assertSame($expected, $filter->toArray());
    }
}
