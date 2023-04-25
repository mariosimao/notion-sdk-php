<?php

namespace Notion\Test\Unit\Blocks\Renderer\Markdown;

use Notion\Blocks\Divider;
use Notion\Blocks\Paragraph;
use Notion\Blocks\Renderer\Markdown\ParagraphRenderer;
use Notion\Common\Equation;
use Notion\Common\RichText;
use PHPUnit\Framework\TestCase;

class ParagraphRendererTest extends TestCase
{
    public function test_render(): void
    {
        $block = Paragraph::create()
            ->addText(RichText::fromString("This ")->bold())
            ->addText(RichText::fromString("is ")->italic())
            ->addText(RichText::fromString("an")->strikeThrough())
            ->addText(RichText::fromString("a ")->underline())
            ->addText(RichText::fromString("paragraph")->code())
            ->addText(RichText::fromEquation(Equation::fromString("e = mc^2")))
            ->addText(RichText::fromString("link")->changeHref("https://example.com"))
            ->addChild(Paragraph::fromString("Child"));

        $markdown = ParagraphRenderer::render($block);

        $expected = <<<MARKDOWN
**This** *is* ~~an~~<u>a </u>`paragraph`\$e = mc^2\$[link](https://example.com)


  Child

MARKDOWN;

        $this->assertSame($expected, $markdown);
    }

    public function test_invalid_block(): void
    {
        $markdown = ParagraphRenderer::render(Divider::create());

        $this->assertSame("", $markdown);
    }
}
