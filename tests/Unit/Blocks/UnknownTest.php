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
        "archived"         => false,
        "has_children"     => false,
        "type"             => "blabla",
        "blabla"           => [],
    ];

    public function test_deserilaize(): void
    {
        $block = BlockFactory::fromArray($this->rawBlock);

        $this->assertInstanceOf(Unknown::class, $block);
        $this->assertSame(BlockType::Unknown, $block->metadata()->type);
        $this->assertEquals($this->rawBlock, $block->toArray());
    }

    public function test_archive(): void
    {
        $block = Unknown::fromArray($this->rawBlock)->archive();

        $this->assertTrue($block->metadata()->archived);
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
