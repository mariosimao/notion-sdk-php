<?php

namespace Notion\Test\Unit\Blocks\Renderer\Markdown;

use Notion\Blocks\Divider;
use Notion\Blocks\Heading3;
use Notion\Blocks\Renderer\Markdown\Heading3Renderer;
use PHPUnit\Framework\TestCase;

class Heading3RendererTest extends TestCase
{
    public function test_render(): void
    {
        $block = Heading3::fromString("Section title");

        $markdown = Heading3Renderer::render($block);

        $expected = <<<MARKDOWN
### Section title
MARKDOWN;

        $this->assertSame($expected, $markdown);
    }

    public function test_invalid_block(): void
    {
        $markdown = Heading3Renderer::render(Divider::create());

        $this->assertSame("", $markdown);
    }
}
