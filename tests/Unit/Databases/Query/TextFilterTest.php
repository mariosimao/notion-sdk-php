<?php

namespace Notion\Test\Unit\Databases\Query;

use Notion\Databases\Query\Operator;
use Notion\Databases\Query\TextFilter;
use PHPUnit\Framework\TestCase;

class TextFilterTest extends TestCase
{
    public function test_empty_filter(): void
    {
        $filter = TextFilter::property("Title");

        $this->assertSame("Title", $filter->propertyName());
        $this->assertSame(Operator::Contains, $filter->operator());
        $this->assertSame("", $filter->value());
    }

    public function test_equals(): void
    {
        $filter = TextFilter::property("Title")->equals("Harry Potter");

        $this->assertSame("Title", $filter->propertyName());
        $this->assertSame(Operator::Equals, $filter->operator());
        $this->assertSame("Harry Potter", $filter->value());

        $expected = [
            "property"  => "Title",
            "rich_text" => [ "equals" => "Harry Potter" ],
        ];
        $this->assertSame($expected, $filter->toArray());
    }

    public function test_does_not_equal(): void
    {
        $filter = TextFilter::property("Title")->doesNotEqual("Harry Potter");

        $expected = [
            "property"  => "Title",
            "rich_text" => [ "does_not_equal" => "Harry Potter" ],
        ];
        $this->assertSame($expected, $filter->toArray());
    }

    public function test_contains(): void
    {
        $filter = TextFilter::property("Title")->contains("Harry Potter");

        $expected = [
            "property"  => "Title",
            "rich_text" => [ "contains" => "Harry Potter" ],
        ];
        $this->assertSame($expected, $filter->toArray());
    }

    public function test_does_not_contain(): void
    {
        $filter = TextFilter::property("Title")->doesNotContain("Harry Potter");

        $expected = [
            "property"  => "Title",
            "rich_text" => [ "does_not_contain" => "Harry Potter" ],
        ];
        $this->assertSame($expected, $filter->toArray());
    }

    public function test_starts_with(): void
    {
        $filter = TextFilter::property("Title")->startsWith("Harry");

        $expected = [
            "property"  => "Title",
            "rich_text" => [ "starts_with" => "Harry" ],
        ];
        $this->assertSame($expected, $filter->toArray());
    }

    public function test_ends_with(): void
    {
        $filter = TextFilter::property("Title")->endsWith("Potter");

        $expected = [
            "property"  => "Title",
            "rich_text" => [ "ends_with" => "Potter" ],
        ];
        $this->assertSame($expected, $filter->toArray());
    }

    public function test_is_empty(): void
    {
        $filter = TextFilter::property("Title")->isEmpty();

        $expected = [
            "property"  => "Title",
            "rich_text" => [ "is_empty" => true ],
        ];
        $this->assertSame($expected, $filter->toArray());
    }

    public function test_is_not_empty(): void
    {
        $filter = TextFilter::property("Title")->isNotEmpty();

        $expected = [
            "property"  => "Title",
            "rich_text" => [ "is_not_empty" => true ],
        ];
        $this->assertSame($expected, $filter->toArray());
    }
}
