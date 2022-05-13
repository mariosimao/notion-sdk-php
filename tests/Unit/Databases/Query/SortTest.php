<?php

namespace Notion\Test\Unit\Databases\Query;

use Notion\Databases\Query\Sort;
use PHPUnit\Framework\TestCase;

class SortTest extends TestCase
{
    public function test_sort_by_property(): void
    {
        $sort = Sort::property("Title");

        $expected = [
            "property" => "Title",
            "direction" => "ascending",
        ];

        $this->assertSame($expected, $sort->toArray());
    }

    public function test_sort_by_created_time(): void
    {
        $sort = Sort::createdTime();

        $expected = [
            "timestamp" => "created_time",
            "direction" => "ascending",
        ];

        $this->assertSame($expected, $sort->toArray());
    }

    public function test_sort_by_last_edited_time(): void
    {
        $sort = Sort::lastEditedTime();

        $expected = [
            "timestamp" => "last_edited_time",
            "direction" => "ascending",
        ];

        $this->assertSame($expected, $sort->toArray());
    }

    public function test_sort_ascending(): void
    {
        $sort = Sort::property("Title")->ascending();

        $this->assertSame("ascending", $sort->toArray()["direction"]);
    }

    public function test_sort_descending(): void
    {
        $sort = Sort::property("Title")->descending();

        $this->assertSame("descending", $sort->toArray()["direction"]);
    }
}
