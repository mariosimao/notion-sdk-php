<?php

namespace Notion\Test\Unit\Blocks;

use Notion\Blocks\BlockMetadata;
use Notion\Blocks\BlockParent;
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

    public function test_new_block_has_no_parent(): void
    {
        $metadata = BlockMetadata::create(BlockType::Paragraph);

        $this->assertNull($metadata->parent);
        $this->assertArrayNotHasKey("parent", $metadata->toArray());
    }

    public function test_parse_parent(): void
    {
        $metadata = BlockMetadata::fromArray([
            "id"               => "04a13895-f072-4814-8af7-cd11af127040",
            "created_time"     => "2021-10-18T17:09:00.000Z",
            "last_edited_time" => "2021-10-18T17:09:00.000Z",
            "in_trash"         => false,
            "has_children"     => false,
            "type"             => "paragraph",
            "parent"           => [
                "type"     => "block_id",
                "block_id" => "7d50a184-5bbe-4d90-8f29-6bec57ed817b",
            ],
        ]);

        $this->assertEquals(BlockParent::block("7d50a184-5bbe-4d90-8f29-6bec57ed817b"), $metadata->parent);
        $this->assertSame(
            ["type" => "block_id", "block_id" => "7d50a184-5bbe-4d90-8f29-6bec57ed817b"],
            $metadata->toArray()["parent"],
        );
    }

    public function test_parent_is_kept_on_changes(): void
    {
        $metadata = BlockMetadata::fromArray([
            "id"               => "04a13895-f072-4814-8af7-cd11af127040",
            "created_time"     => "2021-10-18T17:09:00.000Z",
            "last_edited_time" => "2021-10-18T17:09:00.000Z",
            "in_trash"         => false,
            "has_children"     => false,
            "type"             => "paragraph",
            "parent"           => ["type" => "page_id", "page_id" => "page-id"],
        ]);

        $expected = BlockParent::page("page-id");

        $this->assertEquals($expected, $metadata->delete()->parent);
        $this->assertEquals($expected, $metadata->delete()->restore()->parent);
        $this->assertEquals($expected, $metadata->updateHasChildren(true)->parent);
        $this->assertEquals($expected, $metadata->update()->parent);
    }
}
