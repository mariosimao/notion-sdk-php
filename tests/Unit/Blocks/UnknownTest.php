<?php

namespace Notion\Test\Unit\Blocks;

use Notion\Blocks\BlockFactory;
use Notion\Blocks\BlockType;
use Notion\Blocks\Paragraph;
use Notion\Blocks\Unknown;
use PHPUnit\Framework\TestCase;

class UnknownTest extends TestCase
{
    /** @var array{ type: string, ... } */
    private array $rawBlock = [
        "object"           => "block",
        "id"               => "04a13895-f072-4814-8af7-cd11af127040",
        "created_time"     => "2021-10-18T17:09:00.000Z",
        "last_edited_time" => "2021-10-18T17:09:00.000Z",
        "in_trash"         => false,
        "has_children"     => false,
        "type"             => "blabla",
        "blabla"           => [],
    ];

    public function test_deserilaize(): void
    {
        $block = BlockFactory::fromArray($this->rawBlock);

        $this->assertInstanceOf(Unknown::class, $block);
        $this->assertSame(BlockType::Unknown, $block->metadata()->type);
        $this->assertFalse($block->isUnsupported());
        $this->assertNull($block->unsupportedBlockType);
        $this->assertEquals($this->rawBlock, $block->toArray());
    }

    public function test_deserialize_unsupported_block(): void
    {
        $rawBlock = $this->unsupportedRawBlock();

        $block = BlockFactory::fromArray($rawBlock);

        $this->assertInstanceOf(Unknown::class, $block);
        $this->assertSame(BlockType::Unsupported, $block->metadata()->type);
        $this->assertTrue($block->isUnsupported());
        $this->assertSame("form", $block->unsupportedBlockType);
        $this->assertEquals($rawBlock, $block->toArray());
    }

    public function test_deserialize_unsupported_block_without_block_type(): void
    {
        $rawBlock = $this->unsupportedRawBlock();
        $rawBlock["unsupported"] = [];

        $block = Unknown::fromArray($rawBlock);

        $this->assertTrue($block->isUnsupported());
        $this->assertNull($block->unsupportedBlockType);
        $this->assertEquals($rawBlock, $block->toArray());
    }

    public function test_non_unsupported_block_ignores_unsupported_key(): void
    {
        $rawBlock = $this->rawBlock;
        $rawBlock["unsupported"] = ["block_type" => "form"];

        $block = Unknown::fromArray($rawBlock);

        $this->assertFalse($block->isUnsupported());
        $this->assertNull($block->unsupportedBlockType);
    }

    public function test_transformations_preserve_unsupported_block_type(): void
    {
        $block = Unknown::fromArray($this->unsupportedRawBlock());

        $this->assertSame("form", $block->delete()->unsupportedBlockType);
        $this->assertSame("form", $block->addChild(Paragraph::fromString("a"))->unsupportedBlockType);
        $this->assertSame("form", $block->changeChildren()->unsupportedBlockType);
        $this->assertSame("unsupported", $block->delete()->toArray()["type"]);
    }

    /** @return array{ type: string, ... } */
    private function unsupportedRawBlock(): array
    {
        $rawBlock = $this->rawBlock;
        unset($rawBlock["blabla"]);
        $rawBlock["type"] = "unsupported";
        $rawBlock["unsupported"] = ["block_type" => "form"];

        return $rawBlock;
    }

    public function test_delete(): void
    {
        $block = Unknown::fromArray($this->rawBlock)->delete();

        $this->assertTrue($block->metadata()->inTrash);
    }

    public function test_add_child(): void
    {
        $block = Unknown::fromArray($this->rawBlock);
        $block = $block->addChild(Paragraph::fromString("aaa"));

        $this->assertTrue($block->metadata()->hasChildren);

        /** @psalm-suppress MixedArgument */
        $this->assertCount(1, $block->toArray()["children"]);
    }

    public function test_change_children(): void
    {
        $block = Unknown::fromArray($this->rawBlock);
        $block = $block->changeChildren(Paragraph::fromString("aaa"));

        $this->assertTrue($block->metadata()->hasChildren);

        /** @psalm-suppress MixedArgument */
        $this->assertCount(1, $block->toArray()["children"]);
    }
}
