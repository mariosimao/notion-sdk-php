<?php

namespace Notion\Test\Unit\Blocks\Renderer\Markdown;

use Notion\Blocks\Divider;
use Notion\Blocks\Video;
use Notion\Blocks\Renderer\Markdown\VideoRenderer;
use Notion\Common\File;
use PHPUnit\Framework\TestCase;

class VideoRendererTest extends TestCase
{
    public function test_render(): void
    {
        $block = Video::fromFile(File::createExternal("https://example.com/movie.mp4"));

        $markdown = VideoRenderer::render($block);

        $expected = "![](https://example.com/movie.mp4)";

        $this->assertSame($expected, $markdown);
    }

    public function test_invalid_block(): void
    {
        $markdown = VideoRenderer::render(Divider::create());

        $this->assertSame("", $markdown);
    }
}
