<?php

namespace Notion\Test\Unit\Blocks\Renderer\Markdown;

use Notion\Blocks\Breadcrumb;
use Notion\Blocks\Divider;
use Notion\Blocks\Renderer\Markdown\BreadcrumbRenderer;
use PHPUnit\Framework\TestCase;

class BreadcrumbRendererTest extends TestCase
{
    public function test_render(): void
    {
        $block = Breadcrumb::create();

        $markdown = BreadcrumbRenderer::render($block);

        $expected = "[Breadcrumb]";

        $this->assertSame($expected, $markdown);
    }


    public function test_invalid_block(): void
    {
        $markdown = BreadcrumbRenderer::render(Divider::create());

        $this->assertSame("", $markdown);
    }
}
