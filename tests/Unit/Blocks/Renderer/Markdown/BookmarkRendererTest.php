<?php

namespace Notion\Test\Unit\Blocks\Renderer\Markdown;

use Notion\Blocks\Bookmark;
use Notion\Blocks\Divider;
use Notion\Blocks\Renderer\Markdown\BookmarkRenderer;
use Notion\Blocks\Unknown;
use PHPUnit\Framework\TestCase;

class BookmarkRendererTest extends TestCase
{
    public function test_render(): void
    {
        $bookmark = Bookmark::fromUrl("https://example.com");

        $markdown = BookmarkRenderer::render($bookmark);

        $expected = "<https://example.com>";

        $this->assertSame($expected, $markdown);
    }

    public function test_invalid_block(): void
    {
        $markdown = BookmarkRenderer::render(Divider::create());

        $this->assertSame("", $markdown);
    }
}
