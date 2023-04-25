<?php

namespace Notion\Test\Unit\Blocks\Renderer\Markdown;

use Notion\Blocks\Breadcrumb;
use Notion\Blocks\Divider;
use Notion\Blocks\Renderer\Markdown\DividerRenderer;
use PHPUnit\Framework\TestCase;

class DividerRendererTest extends TestCase
{
    public function test_render(): void
    {
        $block = Divider::create();

        $markdown = DividerRenderer::render($block);

        $expected = "---";

        $this->assertSame($expected, $markdown);
    }

    public function test_invalid_block(): void
    {
        $markdown = DividerRenderer::render(Breadcrumb::create());

        $this->assertSame("", $markdown);
    }
}
