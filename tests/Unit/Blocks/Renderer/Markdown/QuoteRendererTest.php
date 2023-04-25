<?php

namespace Notion\Test\Unit\Blocks\Renderer\Markdown;

use Notion\Blocks\Divider;
use Notion\Blocks\Quote;
use Notion\Blocks\Paragraph;
use Notion\Blocks\Renderer\Markdown\QuoteRenderer;
use PHPUnit\Framework\TestCase;

class QuoteRendererTest extends TestCase
{
    public function test_render(): void
    {
        $block = Quote::fromString("To be or not to be...")
            ->addChild(Paragraph::fromString("Wrote Shakespeare"));

        $markdown = QuoteRenderer::render($block);

        $expected = <<<MARKDOWN
> To be or not to be...
>
> Wrote Shakespeare

MARKDOWN;

        $this->assertSame($expected, $markdown);
    }

    public function test_invalid_block(): void
    {
        $markdown = QuoteRenderer::render(Divider::create());

        $this->assertSame("", $markdown);
    }
}
