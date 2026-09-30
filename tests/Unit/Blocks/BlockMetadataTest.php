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

    public function test_create_has_no_creator_and_editor(): void
    {
        $metadata = BlockMetadata::create(BlockType::Paragraph);

        $this->assertNull($metadata->createdBy);
        $this->assertNull($metadata->lastEditedBy);
        $this->assertArrayNotHasKey("created_by", $metadata->toArray());
        $this->assertArrayNotHasKey("last_edited_by", $metadata->toArray());
    }

    public function test_creator_and_editor(): void
    {
        $array = [
            "type" => "paragraph",
            "id" => "04a13895-f072-4814-8af7-cd11af127040",
            "created_time" => "2021-10-18T17:09:00.000000Z",
            "last_edited_time" => "2021-10-18T17:09:00.000000Z",
            "created_by" => [ "object" => "user", "id" => "creator-id" ],
            "last_edited_by" => [ "object" => "user", "id" => "editor-id" ],
            "in_trash" => false,
            "has_children" => false,
        ];

        $metadata = BlockMetadata::fromArray($array);
        $transformed = $metadata->delete()->restore()->updateHasChildren(true)->update();

        $this->assertSame("creator-id", $transformed->createdBy?->id);
        $this->assertSame("editor-id", $transformed->lastEditedBy?->id);
        $this->assertSame($array["created_by"], $transformed->toArray()["created_by"]);
        $this->assertSame($array["last_edited_by"], $transformed->toArray()["last_edited_by"]);
    }
}
