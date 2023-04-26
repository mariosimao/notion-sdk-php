<?php

namespace Notion\Test\Unit\Blocks\Renderer\Markdown;

use Notion\Blocks\Divider;
use Notion\Blocks\Paragraph;
use Notion\Blocks\Toggle;
use Notion\Blocks\Renderer\Markdown\ToggleRenderer;
use PHPUnit\Framework\TestCase;

class ToggleRendererTest extends TestCase
{
    public function test_render(): void
    {
        $block = Toggle::fromString("Expand")
            ->addChild(Paragraph::fromString("Hidden text"));

        $markdown = ToggleRenderer::render($block);

        $expected = <<<MARKDOWN
<details>
<summary>Expand</summary>

Hidden text
</details>
MARKDOWN;

        $this->assertSame($expected, $markdown);
    }

    public function test_invalid_block(): void
    {
        $markdown = ToggleRenderer::render(Divider::create());

        $this->assertSame("", $markdown);
    }
}
