<?php

namespace Notion\Test\Unit\Blocks\Renderer\Markdown;

use Notion\Blocks\Divider;
use Notion\Blocks\EquationBlock;
use Notion\Blocks\Renderer\Markdown\EquationRenderer;
use PHPUnit\Framework\TestCase;

class EquationRendererTest extends TestCase
{
    public function test_render(): void
    {
        $block = EquationBlock::fromString("e = mc^2");

        $markdown = EquationRenderer::render($block);

        $expected = "$$ e = mc^2 $$";

        $this->assertSame($expected, $markdown);
    }

    public function test_invalid_block(): void
    {
        $markdown = EquationRenderer::render(Divider::create());

        $this->assertSame("", $markdown);
    }
}
