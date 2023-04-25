<?php

namespace Notion\Test\Unit\Blocks\Renderer\Markdown;

use Notion\Blocks\Divider;
use Notion\Blocks\Heading1;
use Notion\Blocks\Renderer\Markdown\Heading1Renderer;
use PHPUnit\Framework\TestCase;

class Heading1RendererTest extends TestCase
{
    public function test_render(): void
    {
        $block = Heading1::fromString("Section title");

        $markdown = Heading1Renderer::render($block);

        $expected = <<<MARKDOWN
# Section title
MARKDOWN;

        $this->assertSame($expected, $markdown);
    }

    public function test_invalid_block(): void
    {
        $markdown = Heading1Renderer::render(Divider::create());

        $this->assertSame("", $markdown);
    }
}
