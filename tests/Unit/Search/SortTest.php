<?php

namespace Notion\Test\Unit\Search;

use Notion\Search\Sort;
use Notion\Search\SortDirection;
use Notion\Search\SortTimestamp;
use PHPUnit\Framework\TestCase;

class SortTest extends TestCase
{
    public function test_create(): void
    {
        $s = Sort::create();

        $this->assertSame(SortDirection::Descending, $s->direction);
        $this->assertSame(SortTimestamp::LastEditedTime, $s->timestamp);
    }

    public function test_by_last_edited_time(): void
    {
        $s = Sort::create()->byLastEditedTime();

        $this->assertSame(SortTimestamp::LastEditedTime, $s->timestamp);
    }

    public function test_ascending(): void
    {
        $s = Sort::create()->ascending();

        $this->assertSame(SortDirection::Ascending, $s->direction);
    }

    public function test_descending(): void
    {
        $s = Sort::create()->descending();

        $this->assertSame(SortDirection::Descending, $s->direction);
    }

    public function test_to_array(): void
    {
        $s = Sort::create();

        $expected = [
            "direction" => "descending",
            "timestamp" => "last_edited_time",
        ];
        $this->assertSame($expected, $s->toArray());
    }
}
