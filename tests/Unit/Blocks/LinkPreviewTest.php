<?php

namespace Notion\Test\Unit\Blocks;

use Notion\Blocks\LinkPreview;
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
        $this->assertTrue($linkPreview->block()->isLinkPreview());
        $this->assertEquals(
            "https://github.com/mariosimao/notion-sdk-php/issues/1",
            $linkPreview->url()
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
}
