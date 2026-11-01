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

    public function test_by_data_sources(): void
    {
        $f = Filter::byDataSources();

        $this->assertSame(FilterValue::DataSource, $f->value);
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

    public function test_by_trash(): void
    {
        $f = Filter::byTrash();

        $this->assertNull($f->value);
        $this->assertNull($f->property);
        $this->assertTrue($f->inTrash);
        $this->assertSame(["in_trash" => true], $f->toArray());
    }

    public function test_by_trash_false(): void
    {
        $f = Filter::byTrash(false);

        $this->assertSame(["in_trash" => false], $f->toArray());
    }

    public function test_pages_in_trash(): void
    {
        $f = Filter::byTrash()->pages();

        $expected = [
            "value"    => "page",
            "property" => "object",
            "in_trash" => true,
        ];
        $this->assertSame($expected, $f->toArray());
    }

    public function test_data_sources_in_trash(): void
    {
        $f = Filter::byDataSources()->changeInTrash(true);

        $this->assertSame(FilterValue::DataSource, $f->value);
        $this->assertTrue($f->inTrash);

        $f = Filter::byTrash()->dataSources();

        $this->assertSame(FilterValue::DataSource, $f->value);
        $this->assertTrue($f->inTrash);
    }

    public function test_object_filter_has_no_trash_status(): void
    {
        $this->assertNull(Filter::byPages()->inTrash);
        $this->assertArrayNotHasKey("in_trash", Filter::byPages()->toArray());
    }
}
