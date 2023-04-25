<?php

namespace Notion\Test\Unit\Blocks\Renderer\Markdown;

use Notion\Blocks\Callout;
use Notion\Blocks\Divider;
use Notion\Blocks\Paragraph;
use Notion\Blocks\Renderer\Markdown\CalloutRenderer;
use PHPUnit\Framework\TestCase;

class CalloutRendererTest extends TestCase
{
    public function test_render(): void
    {
        $block = Callout::fromString("💡", "Tip")
            ->addChild(Paragraph::fromString("A simple tip."));

        $markdown = CalloutRenderer::render($block);

        $expected = <<<MARKDOWN
> 💡 Tip
>
> A simple tip.

MARKDOWN;

        $this->assertSame($expected, $markdown);
    }

    public function test_invalid_block(): void
    {
        $markdown = CalloutRenderer::render(Divider::create());

        $this->assertSame("", $markdown);
    }
}
