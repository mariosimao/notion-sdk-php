<?php

namespace Notion\Test\Unit\Blocks\Renderer\Markdown;

use Notion\Blocks\Divider;
use Notion\Blocks\Embed;
use Notion\Blocks\Renderer\Markdown\EmbedRenderer;
use PHPUnit\Framework\TestCase;

class EmbedRendererTest extends TestCase
{
    public function test_render(): void
    {
        $block = Embed::fromUrl("https://example.com");

        $markdown = EmbedRenderer::render($block);

        $expected = "https://example.com";

        $this->assertSame($expected, $markdown);
    }

    public function test_render_file_upload_without_url(): void
    {
        $block = Embed::fromFileUpload("43833259-72ae-404e-8441-b6577f3159b4");

        $this->assertSame("", EmbedRenderer::render($block));
    }

    public function test_invalid_block(): void
    {
        $markdown = EmbedRenderer::render(Divider::create());

        $this->assertSame("", $markdown);
    }
}
