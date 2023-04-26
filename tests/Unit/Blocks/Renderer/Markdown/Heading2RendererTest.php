<?php

namespace Notion\Test\Unit\Blocks\Renderer\Markdown;

use Notion\Blocks\Divider;
use Notion\Blocks\Heading2;
use Notion\Blocks\Renderer\Markdown\Heading2Renderer;
use PHPUnit\Framework\TestCase;

class Heading2RendererTest extends TestCase
{
    public function test_render(): void
    {
        $block = Heading2::fromString("Section title");

        $markdown = Heading2Renderer::render($block);

        $expected = <<<MARKDOWN
## Section title
MARKDOWN;

        $this->assertSame($expected, $markdown);
    }

    public function test_invalid_block(): void
    {
        $markdown = Heading2Renderer::render(Divider::create());

        $this->assertSame("", $markdown);
    }
}
