<?php

namespace Notion\Test\Unit\Blocks\Renderer\Markdown;

use Notion\Blocks\Audio;
use Notion\Blocks\Divider;
use Notion\Blocks\Renderer\Markdown\AudioRenderer;
use Notion\Common\File;
use PHPUnit\Framework\TestCase;

class AudioRendererTest extends TestCase
{
    public function test_render(): void
    {
        $block = Audio::fromUrl("https://example.com/audio.mp3");

        $markdown = AudioRenderer::render($block);

        $expected = "https://example.com/audio.mp3";

        $this->assertSame($expected, $markdown);
    }

    public function test_render_null_url(): void
    {
        $file = File::createFileUpload("file-upload-id");
        $block = Audio::fromFile($file);

        $markdown = AudioRenderer::render($block);

        $this->assertSame("", $markdown);
    }

    public function test_invalid_block(): void
    {
        $markdown = AudioRenderer::render(Divider::create());

        $this->assertSame("", $markdown);
    }
}
