<?php

namespace Notion\Test\Unit\Blocks\Renderer\Markdown;

use Notion\Blocks\Divider;
use Notion\Blocks\Paragraph;
use Notion\Blocks\Renderer\Markdown\TabRenderer;
use Notion\Blocks\Renderer\MarkdownRenderer;
use Notion\Blocks\Tab;
use Notion\Common\Emoji;
use PHPUnit\Framework\TestCase;

class TabRendererTest extends TestCase
{
    public function test_render(): void
    {
        $block = Tab::create(
            Paragraph::fromString("Overview")
                ->changeIcon(Emoji::fromString("📋"))
                ->addChild(Paragraph::fromString("Tab 1 content")),
            Paragraph::fromString("Details")
                ->addChild(Paragraph::fromString("Tab 2 content")),
        );

        $markdown = TabRenderer::render($block);

        $expected = <<<MARKDOWN
**📋 Overview**

Tab 1 content

**Details**

Tab 2 content

MARKDOWN;

        $this->assertSame($expected, $markdown);
        $this->assertSame($expected, MarkdownRenderer::renderBlock($block));
    }

    public function test_invalid_block(): void
    {
        $markdown = TabRenderer::render(Divider::create());

        $this->assertSame("", $markdown);
    }
}
