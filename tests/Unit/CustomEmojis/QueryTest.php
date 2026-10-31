<?php

namespace Notion\Test\Unit\CustomEmojis;

use InvalidArgumentException;
use Notion\CustomEmojis\Query;
use PHPUnit\Framework\TestCase;

class QueryTest extends TestCase
{
    public function test_all(): void
    {
        $query = Query::all();

        $this->assertNull($query->name);
        $this->assertNull($query->startCursor);
        $this->assertNull($query->pageSize);
        $this->assertSame([], $query->toArray());
    }

    public function test_filter_by_name(): void
    {
        $query = Query::name("party-parrot");

        $this->assertSame("party-parrot", $query->name);
        $this->assertSame(["name" => "party-parrot"], $query->toArray());
    }

    public function test_change_and_remove_name(): void
    {
        $query = Query::all()->changeName("party-parrot");
        $this->assertSame("party-parrot", $query->name);

        $query = $query->removeName();
        $this->assertNull($query->name);
    }

    public function test_pagination(): void
    {
        $query = Query::name("party-parrot")
            ->changePageSize(10)
            ->changeStartCursor("cursor-id");

        $this->assertSame([
            "name"         => "party-parrot",
            "start_cursor" => "cursor-id",
            "page_size"    => 10,
        ], $query->toArray());

        $this->assertNull($query->removeStartCursor()->startCursor);
    }

    public function test_page_size_too_small(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Query::all()->changePageSize(0);
    }

    public function test_page_size_too_big(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Query::all()->changePageSize(101);
    }
}
