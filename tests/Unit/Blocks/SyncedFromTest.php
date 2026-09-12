<?php

namespace Notion\Test\Unit\Blocks;

use Notion\Blocks\SyncedFrom;
use PHPUnit\Framework\TestCase;

class SyncedFromTest extends TestCase
{
    public function test_create(): void
    {
        $from = SyncedFrom::create("89b25dc4-6b22-4467-8cfb-663806bf203e");

        $this->assertSame("89b25dc4-6b22-4467-8cfb-663806bf203e", $from->blockId);
        $this->assertSame("block_id", $from->type);
    }

    public function test_from_array(): void
    {
        $array = [
            "type"     => "block_id",
            "block_id" => "89b25dc4-6b22-4467-8cfb-663806bf203e",
        ];
        $from = SyncedFrom::fromArray($array);

        $this->assertSame("89b25dc4-6b22-4467-8cfb-663806bf203e", $from->blockId);
        $this->assertSame("block_id", $from->type);
    }

    public function test_from_array_without_type(): void
    {
        $array = [
            "block_id" => "89b25dc4-6b22-4467-8cfb-663806bf203e",
        ];
        $from = SyncedFrom::fromArray($array);

        $this->assertSame("89b25dc4-6b22-4467-8cfb-663806bf203e", $from->blockId);
        $this->assertSame("block_id", $from->type);
    }

    public function test_to_array(): void
    {
        $from = SyncedFrom::create("89b25dc4-6b22-4467-8cfb-663806bf203e");
        $expected = [
            "type"     => "block_id",
            "block_id" => "89b25dc4-6b22-4467-8cfb-663806bf203e",
        ];

        $this->assertSame($expected, $from->toArray());
    }

    public function test_change_block_id(): void
    {
        $from = SyncedFrom::create("89b25dc4-6b22-4467-8cfb-663806bf203e");
        $new = $from->changeBlockId("d368e7d2-9706-4fc6-bca5-bdf95db0f2b2");

        $this->assertSame("d368e7d2-9706-4fc6-bca5-bdf95db0f2b2", $new->blockId);
        $this->assertSame("89b25dc4-6b22-4467-8cfb-663806bf203e", $from->blockId);
    }
}
