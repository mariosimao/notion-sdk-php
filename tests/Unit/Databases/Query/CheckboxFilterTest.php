<?php

namespace Notion\Test\Unit\Databases\Query;

use Notion\Databases\Query\CheckboxFilter;
use Notion\Databases\Query\Operator;
use PHPUnit\Framework\TestCase;

class CheckboxFilterTest extends TestCase
{
    public function test_empty_filter(): void
    {
        $filter = CheckboxFilter::property("Done");

        $this->assertSame("property", $filter->propertyType());
        $this->assertSame("Done", $filter->propertyName());
        $this->assertSame(Operator::Equals, $filter->operator());
        $this->assertTrue($filter->value());
    }

    public function test_equals(): void
    {
        $filter = CheckboxFilter::property("Done")->equals(true);

        $expected = [
            "property" => "Done",
            "checkbox" => [ "equals" => true ],
        ];
        $this->assertSame($expected, $filter->toArray());
    }

    public function test_does_not_equal(): void
    {
        $filter = CheckboxFilter::property("Done")->doesNotEqual(true);

        $expected = [
            "property" => "Done",
            "checkbox" => [ "does_not_equal" => true ],
        ];
        $this->assertSame($expected, $filter->toArray());
    }
}
