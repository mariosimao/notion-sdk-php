<?php

namespace Notion\Test\Unit\Pages;

use Notion\Pages\PagePosition;
use Notion\Pages\PagePositionType;
use PHPUnit\Framework\TestCase;

class PagePositionTest extends TestCase
{
    public function test_after_block(): void
    {
        $position = PagePosition::afterBlock("058d158b-09de-4d69-be07-901c20a7ca5c");

        $this->assertTrue($position->isAfterBlock());
        $this->assertFalse($position->isPageStart());
        $this->assertFalse($position->isPageEnd());
        $this->assertSame(PagePositionType::AfterBlock, $position->type);
        $this->assertSame("058d158b-09de-4d69-be07-901c20a7ca5c", $position->blockId);
        $this->assertSame(
            [
                "type" => "after_block",
                "after_block" => ["id" => "058d158b-09de-4d69-be07-901c20a7ca5c"],
            ],
            $position->toArray(),
        );
    }

    public function test_page_start(): void
    {
        $position = PagePosition::pageStart();

        $this->assertTrue($position->isPageStart());
        $this->assertFalse($position->isAfterBlock());
        $this->assertFalse($position->isPageEnd());
        $this->assertNull($position->blockId);
        $this->assertSame(["type" => "page_start"], $position->toArray());
    }

    public function test_page_end(): void
    {
        $position = PagePosition::pageEnd();

        $this->assertTrue($position->isPageEnd());
        $this->assertFalse($position->isAfterBlock());
        $this->assertFalse($position->isPageStart());
        $this->assertNull($position->blockId);
        $this->assertSame(["type" => "page_end"], $position->toArray());
    }
}
