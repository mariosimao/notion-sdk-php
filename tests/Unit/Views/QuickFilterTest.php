<?php

namespace Notion\Test\Unit\Views;

use Notion\DataSources\Query\DateFilter;
use Notion\DataSources\Query\TextFilter;
use Notion\Views\QuickFilter;
use PHPUnit\Framework\TestCase;

class QuickFilterTest extends TestCase
{
    public function test_from_filter(): void
    {
        $quickFilter = QuickFilter::fromFilter(TextFilter::property("Name")->contains("foo"));

        $this->assertSame("Name", $quickFilter->property);
        $this->assertSame([ "rich_text" => [ "contains" => "foo" ] ], $quickFilter->toArray());
    }

    public function test_from_timestamp_filter(): void
    {
        $quickFilter = QuickFilter::fromFilter(DateFilter::createdTime()->isNotEmpty());

        $this->assertSame("created_time", $quickFilter->property);
        $this->assertArrayNotHasKey("timestamp", (array) $quickFilter->toArray());
    }

    public function test_without_criteria_is_encoded_as_empty_object(): void
    {
        $quickFilter = QuickFilter::withoutCriteria("Tags");

        $this->assertSame("{}", json_encode($quickFilter->toArray()));
    }
}
