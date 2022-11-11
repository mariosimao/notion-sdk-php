<?php

namespace Notion\Test\Unit\Blocks;

use Notion\Blocks\BlockFactory;
use Notion\Exceptions\BlockException;
use Notion\Blocks\LinkPreview;
use Notion\Blocks\Paragraph;
use PHPUnit\Framework\TestCase;

class LinkPreviewTest extends TestCase
{
    public function test_array_conversion(): void
    {
        $array = [
            "object"           => "block",
            "id"               => "04a13895-f072-4814-8af7-cd11af127040",
            "created_time"     => "2021-10-18T17:09:00.000000Z",
            "last_edited_time" => "2021-10-18T17:09:00.000000Z",
            "archived"         => false,
            "has_children"     => false,
            "type"             => "link_preview",
            "link_preview"     => [
                "url" => "https://github.com/mariosimao/notion-sdk-php/issues/1",
            ],
        ];

        $linkPreview = LinkPreview::fromArray($array);

        $this->assertEquals($array, $linkPreview->toArray());
        $this->assertEquals(
            "https://github.com/mariosimao/notion-sdk-php/issues/1",
            $linkPreview->url,
        );
    }

    public function test_from_invalid_type(): void
    {
        $array = [
            "object"           => "block",
            "id"               => "04a13895-f072-4814-8af7-cd11af127040",
            "created_time"     => "2021-10-18T17:09:00.000000Z",
            "last_edited_time" => "2021-10-18T17:09:00.000000Z",
            "archived"         => false,
            "has_children"     => false,
            "type"             => "heading1",
            "link_preview"     => [
                "url" => "https://github.com/mariosimao/notion-sdk-php/issues/1",
            ],
        ];

        $this->expectException(\Exception::class);
        LinkPreview::fromArray($array);
    }

    public function test_create_change_factory(): void
    {
        $array = [
            "object"           => "block",
            "id"               => "04a13895-f072-4814-8af7-cd11af127040",
            "created_time"     => "2021-10-18T17:09:00.000000Z",
            "last_edited_time" => "2021-10-18T17:09:00.000000Z",
            "archived"         => false,
            "has_children"     => false,
            "type"             => "link_preview",
            "link_preview"     => [
                "url" => "https://github.com/mariosimao/notion-sdk-php/issues/1",
            ],
        ];

        $block = BlockFactory::fromArray($array);

        $this->assertInstanceOf(LinkPreview::class, $block);
    }

    public function test_no_children_support(): void
    {
        $array = [
            "object"           => "block",
            "id"               => "04a13895-f072-4814-8af7-cd11af127040",
            "created_time"     => "2021-10-18T17:09:00.000000Z",
            "last_edited_time" => "2021-10-18T17:09:00.000000Z",
            "archived"         => false,
            "has_children"     => false,
            "type"             => "link_preview",
            "link_preview"     => [
                "url" => "https://github.com/mariosimao/notion-sdk-php/issues/1",
            ],
        ];

        $block = LinkPreview::fromArray($array);

        $this->expectException(BlockException::class);
        /** @psalm-suppress UnusedMethodCall */
        $block->changeChildren();
    }

    public function test_no_children_support_2(): void
    {
        $array = [
            "object"           => "block",
            "id"               => "04a13895-f072-4814-8af7-cd11af127040",
            "created_time"     => "2021-10-18T17:09:00.000000Z",
            "last_edited_time" => "2021-10-18T17:09:00.000000Z",
            "archived"         => false,
            "has_children"     => false,
            "type"             => "link_preview",
            "link_preview"     => [
                "url" => "https://github.com/mariosimao/notion-sdk-php/issues/1",
            ],
        ];

        $block = LinkPreview::fromArray($array);

        $this->expectException(BlockException::class);
        /** @psalm-suppress UnusedMethodCall */
        $block->addChild(Paragraph::create());
    }

    public function test_archive(): void
    {
        $block = LinkPreview::fromArray([
            "object"           => "block",
            "id"               => "04a13895-f072-4814-8af7-cd11af127040",
            "created_time"     => "2021-10-18T17:09:00.000000Z",
            "last_edited_time" => "2021-10-18T17:09:00.000000Z",
            "archived"         => false,
            "has_children"     => false,
            "type"             => "link_preview",
            "link_preview"     => [
                "url" => "https://github.com/mariosimao/notion-sdk-php/issues/1",
            ],
        ]);

        $block = $block->archive();

        $this->assertTrue($block->metadata()->archived);
    }
}
