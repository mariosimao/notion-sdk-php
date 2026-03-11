<?php

namespace Notion\Test\Unit\Blocks;

use Notion\Blocks\BlockMetadata;
use Notion\Blocks\BlockType;
use Notion\Exceptions\BlockException;
use PHPUnit\Framework\TestCase;

class BlockMetadataTest extends TestCase
{
    public function test_restore(): void
    {
        $metadata = BlockMetadata::create(BlockType::Paragraph);

        $metadata = $metadata->delete();
        $metadata = $metadata->restore();

        $this->assertFalse($metadata->inTrash);
    }

    public function test_check_type(): void
    {
        $metadata = BlockMetadata::create(BlockType::Paragraph);

        $this->expectException(BlockException::class);
        $metadata->checkType(BlockType::BulletedListItem);
    }
}
