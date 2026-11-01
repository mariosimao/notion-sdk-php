<?php

namespace Notion\Test\Unit\Blocks;

use Notion\Blocks\BlockList;
use Notion\Blocks\Divider;
use PHPUnit\Framework\TestCase;

class BlockListTest extends TestCase
{
    public function test_from_array(): void
    {
        $list = BlockList::fromArray([
            "object"      => "list",
            "results"     => [
                [
                    "object"           => "block",
                    "id"               => "04a13895-f072-4814-8af7-cd11af127040",
                    "created_time"     => "2021-10-18T17:09:00.000Z",
                    "last_edited_time" => "2021-10-18T17:09:00.000Z",
                    "in_trash"         => false,
                    "has_children"     => false,
                    "type"             => "divider",
                    "divider"          => new \stdClass(),
                ],
            ],
            "next_cursor" => "fe2cc560-036c-44cd-90e8-294d5a74cebc",
            "has_more"    => true,
            "type"        => "block",
            "block"       => new \stdClass(),
        ]);

        $this->assertCount(1, $list->results);
        $this->assertInstanceOf(Divider::class, $list->results[0]);
        $this->assertSame("04a13895-f072-4814-8af7-cd11af127040", $list->results[0]->metadata()->id);
        $this->assertSame("fe2cc560-036c-44cd-90e8-294d5a74cebc", $list->nextCursor);
        $this->assertTrue($list->hasMore);
    }

    public function test_from_array_last_page(): void
    {
        $list = BlockList::fromArray([
            "object"      => "list",
            "results"     => [],
            "next_cursor" => null,
            "has_more"    => false,
        ]);

        $this->assertSame([], $list->results);
        $this->assertNull($list->nextCursor);
        $this->assertFalse($list->hasMore);
    }

    public function test_create(): void
    {
        $divider = Divider::create();
        $list = BlockList::create([ $divider ], "cursor", true);

        $this->assertSame([ $divider ], $list->results);
        $this->assertSame("cursor", $list->nextCursor);
        $this->assertTrue($list->hasMore);
    }

    public function test_create_defaults(): void
    {
        $list = BlockList::create([]);

        $this->assertSame([], $list->results);
        $this->assertNull($list->nextCursor);
        $this->assertFalse($list->hasMore);
    }
}
