<?php

namespace Notion\Test\Unit\Blocks\Renderer\Markdown;

use Notion\Blocks\Divider;
use Notion\Blocks\Image;
use Notion\Blocks\Renderer\Markdown\ImageRenderer;
use Notion\Common\File;
use PHPUnit\Framework\TestCase;

class ImageRendererTest extends TestCase
{
    public function test_render(): void
    {
        $block = Image::fromFile(File::createExternal("https://example.com/dog.jpg"));

        $markdown = ImageRenderer::render($block);

        $expected = "![](https://example.com/dog.jpg)";

        $this->assertSame($expected, $markdown);
    }

    public function test_invalid_block(): void
    {
        $markdown = ImageRenderer::render(Divider::create());

        $this->assertSame("", $markdown);
    }
}
