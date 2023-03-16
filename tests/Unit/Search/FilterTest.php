<?php

namespace Notion\Test\Unit\Search;

use Notion\Search\Filter;
use Notion\Search\FilterProperty;
use Notion\Search\FilterValue;
use PHPUnit\Framework\TestCase;

class FilterTest extends TestCase
{
    public function test_by_pages(): void
    {
        $f = Filter::byPages();

        $this->assertSame(FilterValue::Page, $f->value);
        $this->assertSame(FilterProperty::Object, $f->property);
    }

    public function test_by_databases(): void
    {
        $f = Filter::byDatabases();

        $this->assertSame(FilterValue::Database, $f->value);
        $this->assertSame(FilterProperty::Object, $f->property);
    }

    public function test_to_array(): void
    {
        $f = Filter::byPages();

        $expected = [
            "value"    => "page",
            "property" => "object",
        ];
        $this->assertSame($expected, $f->toArray());
    }
}
