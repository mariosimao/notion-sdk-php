<?php

namespace Notion\Test\Unit\Blocks\Renderer\Markdown;

use Notion\Blocks\Divider;
use Notion\Blocks\LinkPreview;
use Notion\Blocks\Renderer\Markdown\LinkPreviewRenderer;
use PHPUnit\Framework\TestCase;

class LinkPreviewRendererTest extends TestCase
{
    public function test_render(): void
    {
        $block = LinkPreview::fromArray([
            "id"                => "abc123",
            "created_time"      => "2023-01-01 00:00:00",
            "last_edited_time"  => "2023-01-01 00:00:00",
            "archived"          => false,
            "has_children"      => false,
            "type"              => "link_preview",
            "link_preview"    => [
                "url" => "https://example.com"
            ],
        ]);

        $markdown = LinkPreviewRenderer::render($block);

        $expected = "https://example.com";

        $this->assertSame($expected, $markdown);
    }

    public function test_invalid_block(): void
    {
        $markdown = LinkPreviewRenderer::render(Divider::create());

        $this->assertSame("", $markdown);
    }
}
