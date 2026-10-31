<?php

namespace Notion\Test\Unit\Blocks;

use Notion\Blocks\Position;
use Notion\Blocks\PositionType;
use PHPUnit\Framework\TestCase;

class PositionTest extends TestCase
{
    public function test_start(): void
    {
        $position = Position::start();

        $this->assertSame(PositionType::Start, $position->type);
        $this->assertNull($position->afterBlockId);
        $this->assertSame([ "type" => "start" ], $position->toArray());
    }

    public function test_end(): void
    {
        $position = Position::end();

        $this->assertSame(PositionType::End, $position->type);
        $this->assertNull($position->afterBlockId);
        $this->assertSame([ "type" => "end" ], $position->toArray());
    }

    public function test_after_block(): void
    {
        $position = Position::afterBlock("b5d8fd79-6a2f-4b5c-9c1f-0f0d6a0b1c2d");

        $this->assertSame(PositionType::AfterBlock, $position->type);
        $this->assertSame("b5d8fd79-6a2f-4b5c-9c1f-0f0d6a0b1c2d", $position->afterBlockId);
        $this->assertSame(
            [
                "type"        => "after_block",
                "after_block" => [ "id" => "b5d8fd79-6a2f-4b5c-9c1f-0f0d6a0b1c2d" ],
            ],
            $position->toArray(),
        );
    }
}
