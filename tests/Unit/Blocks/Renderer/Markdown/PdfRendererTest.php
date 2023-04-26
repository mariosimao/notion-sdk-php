<?php

namespace Notion\Test\Unit\Blocks\Renderer\Markdown;

use Notion\Blocks\Divider;
use Notion\Blocks\Pdf;
use Notion\Blocks\Renderer\Markdown\PdfRenderer;
use Notion\Common\File;
use PHPUnit\Framework\TestCase;

class PdfRendererTest extends TestCase
{
    public function test_render(): void
    {
        $block = Pdf::fromFile(File::createExternal("https://example.com/file.pdf"));

        $markdown = PdfRenderer::render($block);

        $expected = "https://example.com/file.pdf";

        $this->assertSame($expected, $markdown);
    }

    public function test_invalid_block(): void
    {
        $markdown = PdfRenderer::render(Divider::create());

        $this->assertSame("", $markdown);
    }
}
