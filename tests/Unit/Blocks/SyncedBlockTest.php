<?php

namespace Notion\Test\Unit\Blocks;

use Notion\Blocks\BlockFactory;
use Notion\Blocks\BlockType;
use Notion\Blocks\Paragraph;
use Notion\Blocks\SyncedBlock;
use Notion\Blocks\SyncedFrom;
use Notion\Exceptions\BlockException;
use PHPUnit\Framework\TestCase;

class SyncedBlockTest extends TestCase
{
    public function test_create_original_without_children(): void
    {
        $block = SyncedBlock::createOriginal();

        $this->assertSame(BlockType::SyncedBlock, $block->metadata()->type);
        $this->assertTrue($block->isOriginal());
        $this->assertFalse($block->isReference());
        $this->assertNull($block->syncedFrom);
        $this->assertNull($block->originalBlockId());
        $this->assertEmpty($block->children);
        $this->assertFalse($block->metadata()->hasChildren);
        $this->assertFalse($block->metadata()->inTrash);
    }

    public function test_create_original_with_children(): void
    {
        $child = Paragraph::fromString("Synced block paragraph");
        $block = SyncedBlock::createOriginal($child);

        $this->assertTrue($block->isOriginal());
        $this->assertCount(1, $block->children);
        $this->assertSame($child, $block->children[0]);
        $this->assertTrue($block->metadata()->hasChildren);
    }

    public function test_create_reference_from_string(): void
    {
        $block = SyncedBlock::createReference("89b25dc4-6b22-4467-8cfb-663806bf203e");

        $this->assertSame(BlockType::SyncedBlock, $block->metadata()->type);
        $this->assertFalse($block->isOriginal());
        $this->assertTrue($block->isReference());
        $this->assertNotNull($block->syncedFrom);
        $this->assertSame("89b25dc4-6b22-4467-8cfb-663806bf203e", $block->syncedFrom->blockId);
        $this->assertSame("89b25dc4-6b22-4467-8cfb-663806bf203e", $block->originalBlockId());
        $this->assertEmpty($block->children);
        $this->assertFalse($block->metadata()->hasChildren);
    }

    public function test_create_reference_from_synced_block(): void
    {
        $rawOriginal = [
            "object"           => "block",
            "id"               => "7af38973-3787-41b3-bd75-0ed3a1edfac9",
            "created_time"     => "2021-11-17T22:17:00.000Z",
            "last_edited_time" => "2021-11-17T22:17:00.000Z",
            "in_trash"         => false,
            "has_children"     => false,
            "type"             => "synced_block",
            "synced_block"     => [
                "synced_from" => null,
            ],
        ];
        $original = SyncedBlock::fromArray($rawOriginal);

        $reference = SyncedBlock::createReference($original);

        $this->assertTrue($reference->isReference());
        $this->assertSame("7af38973-3787-41b3-bd75-0ed3a1edfac9", $reference->originalBlockId());
    }

    public function test_create_reference_from_reference_synced_block(): void
    {
        $ref1 = SyncedBlock::createReference("original-id-123");
        $ref2 = SyncedBlock::createReference($ref1);

        $this->assertTrue($ref2->isReference());
        $this->assertSame("original-id-123", $ref2->originalBlockId());
    }

    public function test_add_child_on_original(): void
    {
        $block = SyncedBlock::createOriginal();
        $child = Paragraph::fromString("Nested item");

        $newBlock = $block->addChild($child);

        $this->assertEmpty($block->children);
        $this->assertCount(1, $newBlock->children);
        $this->assertSame($child, $newBlock->children[0]);
        $this->assertTrue($newBlock->metadata()->hasChildren);
    }

    public function test_change_children_on_original(): void
    {
        $block = SyncedBlock::createOriginal();
        $child1 = Paragraph::fromString("Item 1");
        $child2 = Paragraph::fromString("Item 2");

        $newBlock = $block->changeChildren($child1, $child2);
        $this->assertCount(2, $newBlock->children);
        $this->assertTrue($newBlock->metadata()->hasChildren);

        $emptyBlock = $newBlock->changeChildren();
        $this->assertEmpty($emptyBlock->children);
        $this->assertFalse($emptyBlock->metadata()->hasChildren);
    }

    public function test_add_child_on_reference_throws_exception(): void
    {
        $block = SyncedBlock::createReference("89b25dc4-6b22-4467-8cfb-663806bf203e");

        $this->expectException(BlockException::class);
        $block->addChild(Paragraph::fromString("Child"));
    }

    public function test_change_children_on_reference_throws_exception(): void
    {
        $block = SyncedBlock::createReference("89b25dc4-6b22-4467-8cfb-663806bf203e");

        $this->expectException(BlockException::class);
        $block->changeChildren(Paragraph::fromString("Child"));
    }

    public function test_change_synced_from(): void
    {
        $block = SyncedBlock::createReference("old-id");

        $withString = $block->changeSyncedFrom("new-id-1");
        $this->assertSame("new-id-1", $withString->originalBlockId());

        $withObject = $block->changeSyncedFrom(SyncedFrom::create("new-id-2"));
        $this->assertSame("new-id-2", $withObject->originalBlockId());
    }

    public function test_to_original(): void
    {
        $ref = SyncedBlock::createReference("original-id");
        $child = Paragraph::fromString("New original child");

        $original = $ref->toOriginal($child);

        $this->assertTrue($original->isOriginal());
        $this->assertFalse($original->isReference());
        $this->assertNull($original->syncedFrom);
        $this->assertCount(1, $original->children);
        $this->assertTrue($original->metadata()->hasChildren);
    }

    public function test_to_reference(): void
    {
        $original = SyncedBlock::createOriginal(Paragraph::fromString("Child"));

        $ref = $original->toReference("target-id");

        $this->assertFalse($ref->isOriginal());
        $this->assertTrue($ref->isReference());
        $this->assertSame("target-id", $ref->originalBlockId());
        $this->assertEmpty($ref->children);
        $this->assertFalse($ref->metadata()->hasChildren);
    }

    public function test_to_reference_from_synced_from_and_synced_block(): void
    {
        $original = SyncedBlock::createOriginal();

        $refFromObject = $original->toReference(SyncedFrom::create("target-1"));
        $this->assertSame("target-1", $refFromObject->originalBlockId());

        $refFromRef = $original->toReference($refFromObject);
        $this->assertSame("target-1", $refFromRef->originalBlockId());
    }

    public function test_from_array_original_with_children(): void
    {
        $array = [
            "object"           => "block",
            "id"               => "7af38973-3787-41b3-bd75-0ed3a1edfac9",
            "created_time"     => "2021-11-17T22:17:00.000Z",
            "last_edited_time" => "2021-11-17T22:17:00.000Z",
            "in_trash"         => false,
            "has_children"     => true,
            "type"             => "synced_block",
            "synced_block"     => [
                "synced_from" => null,
                "children"    => [
                    [
                        "object"           => "block",
                        "id"               => "89b25dc4-6b22-4467-8cfb-663806bf203e",
                        "created_time"     => "2021-11-17T22:17:00.000Z",
                        "last_edited_time" => "2021-11-17T22:17:00.000Z",
                        "in_trash"         => false,
                        "has_children"     => false,
                        "type"             => "paragraph",
                        "paragraph"        => [
                            "rich_text" => [
                                [
                                    "type"        => "text",
                                    "text"        => [ "content" => "Child content", "link" => null ],
                                    "annotations" => [
                                        "bold"          => false,
                                        "italic"        => false,
                                        "strikethrough" => false,
                                        "underline"     => false,
                                        "code"          => false,
                                        "color"         => "default",
                                    ],
                                    "plain_text"  => "Child content",
                                    "href"        => null,
                                ],
                            ],
                            "color"     => "default",
                        ],
                    ],
                ],
            ],
        ];

        $block = SyncedBlock::fromArray($array);

        $this->assertTrue($block->isOriginal());
        $this->assertNull($block->syncedFrom);
        $this->assertCount(1, $block->children);
        $this->assertInstanceOf(Paragraph::class, $block->children[0]);
        $this->assertSame("Child content", $block->children[0]->toString());

        $this->assertEquals($block, BlockFactory::fromArray($array));
    }

    public function test_from_array_original_without_children_key(): void
    {
        $array = [
            "object"           => "block",
            "id"               => "7af38973-3787-41b3-bd75-0ed3a1edfac9",
            "created_time"     => "2021-11-17T22:17:00.000Z",
            "last_edited_time" => "2021-11-17T22:17:00.000Z",
            "in_trash"         => false,
            "has_children"     => false,
            "type"             => "synced_block",
            "synced_block"     => [
                "synced_from" => null,
            ],
        ];

        $block = SyncedBlock::fromArray($array);

        $this->assertTrue($block->isOriginal());
        $this->assertEmpty($block->children);
    }

    public function test_from_array_reference(): void
    {
        $array = [
            "object"           => "block",
            "id"               => "7af38973-3787-41b3-bd75-0ed3a1edfac9",
            "created_time"     => "2021-11-17T22:17:00.000Z",
            "last_edited_time" => "2021-11-17T22:17:00.000Z",
            "in_trash"         => false,
            "has_children"     => true,
            "type"             => "synced_block",
            "synced_block"     => [
                "synced_from" => [
                    "type"     => "block_id",
                    "block_id" => "89b25dc4-6b22-4467-8cfb-663806bf203e",
                ],
            ],
        ];

        $block = SyncedBlock::fromArray($array);

        $this->assertTrue($block->isReference());
        $this->assertSame("89b25dc4-6b22-4467-8cfb-663806bf203e", $block->originalBlockId());
        $this->assertEmpty($block->children);

        $this->assertEquals($block, BlockFactory::fromArray($array));
    }

    public function test_to_array_original(): void
    {
        $child = Paragraph::fromString("Child");
        $block = SyncedBlock::createOriginal($child);

        $array = $block->toArray();

        $this->assertSame("block", $array["object"]);
        $this->assertSame("synced_block", $array["type"]);
        $this->assertArrayHasKey("synced_block", $array);

        /** @var array{ synced_from: mixed, children: list<array{ type: string, ... }> } $syncedBlock */
        $syncedBlock = $array["synced_block"];
        $this->assertNull($syncedBlock["synced_from"]);
        $this->assertCount(1, $syncedBlock["children"]);
        $this->assertSame("paragraph", $syncedBlock["children"][0]["type"]);
    }

    public function test_to_array_reference(): void
    {
        $block = SyncedBlock::createReference("89b25dc4-6b22-4467-8cfb-663806bf203e");

        $array = $block->toArray();

        $this->assertSame("block", $array["object"]);
        $this->assertSame("synced_block", $array["type"]);
        $this->assertArrayHasKey("synced_block", $array);

        /** @var array{ synced_from: array{ type: string, block_id: string }, children?: mixed } $syncedBlock */
        $syncedBlock = $array["synced_block"];
        $this->assertSame(
            [
                "type"     => "block_id",
                "block_id" => "89b25dc4-6b22-4467-8cfb-663806bf203e",
            ],
            $syncedBlock["synced_from"]
        );
        $this->assertArrayNotHasKey("children", $syncedBlock);
    }

    public function test_delete(): void
    {
        $block = SyncedBlock::createOriginal();

        $deleted = $block->delete();
        $this->assertTrue($deleted->metadata()->inTrash);
    }

    public function test_archive(): void
    {
        $block = SyncedBlock::createOriginal();

        /** @psalm-suppress DeprecatedMethod */
        $archived = $block->archive();
        $this->assertTrue($archived->metadata()->inTrash);
    }

    public function test_wrong_type_throws_exception(): void
    {
        $array = [
            "object"           => "block",
            "id"               => "7af38973-3787-41b3-bd75-0ed3a1edfac9",
            "created_time"     => "2021-11-17T22:17:00.000Z",
            "last_edited_time" => "2021-11-17T22:17:00.000Z",
            "in_trash"         => false,
            "has_children"     => false,
            "type"             => "paragraph",
            "synced_block"     => [
                "synced_from" => null,
            ],
        ];

        $this->expectException(BlockException::class);
        SyncedBlock::fromArray($array);
    }
}
