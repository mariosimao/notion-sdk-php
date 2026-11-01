<?php

namespace Notion\Test\Unit\Blocks\Renderer\Markdown;

use Notion\Blocks\Divider;
use Notion\Blocks\Heading4;
use Notion\Blocks\Renderer\Markdown\Heading4Renderer;
use PHPUnit\Framework\TestCase;

class Heading4RendererTest extends TestCase
{
    public function test_render(): void
    {
        $block = Heading4::fromString("Section title");

        $markdown = Heading4Renderer::render($block);

        $expected = <<<MARKDOWN
#### Section title
MARKDOWN;

        $this->assertSame($expected, $markdown);
    }

    public function test_invalid_block(): void
    {
        $markdown = Heading4Renderer::render(Divider::create());

        $this->assertSame("", $markdown);
    }
}
