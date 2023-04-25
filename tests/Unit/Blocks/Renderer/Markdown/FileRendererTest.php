<?php

namespace Notion\Test\Unit\Blocks\Renderer\Markdown;

use Notion\Blocks\Divider;
use Notion\Blocks\FileBlock;
use Notion\Blocks\Renderer\Markdown\FileRenderer;
use Notion\Common\File;
use PHPUnit\Framework\TestCase;

class FileRendererTest extends TestCase
{
    public function test_render(): void
    {
        $block = FileBlock::fromFile(File::createExternal("https://example.com/my-file.doc"));

        $markdown = FileRenderer::render($block);

        $expected = "https://example.com/my-file.doc";

        $this->assertSame($expected, $markdown);
    }

    public function test_invalid_block(): void
    {
        $markdown = FileRenderer::render(Divider::create());

        $this->assertSame("", $markdown);
    }
}
