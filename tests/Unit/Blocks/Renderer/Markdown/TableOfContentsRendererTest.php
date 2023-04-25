<?php

namespace Notion\Test\Unit\Blocks\Renderer\Markdown;

use Notion\Blocks\Divider;
use Notion\Blocks\TableOfContents;
use Notion\Blocks\Renderer\Markdown\TableOfContentsRenderer;
use PHPUnit\Framework\TestCase;

class TableOfContentsRendererTest extends TestCase
{
    public function test_render(): void
    {
        $block = TableOfContents::create();

        $markdown = TableOfContentsRenderer::render($block);

        $expected = "[TableOfContents]";

        $this->assertSame($expected, $markdown);
    }

    public function test_invalid_block(): void
    {
        $markdown = TableOfContentsRenderer::render(Divider::create());

        $this->assertSame("", $markdown);
    }
}
