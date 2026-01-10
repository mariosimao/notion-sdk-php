<?php

namespace Notion\Test\Unit\Blocks\Renderer;

use Notion\Blocks\BlockType;
use Notion\Blocks\BulletedListItem;
use Notion\Blocks\Heading1;
use Notion\Blocks\Heading2;
use Notion\Blocks\Paragraph;
use Notion\Blocks\Renderer\Markdown\Heading1Renderer;
use Notion\Blocks\Renderer\MarkdownRenderer;
use PHPUnit\Framework\TestCase;

class MarkdownRendererTest extends TestCase
{
    public function test_render(): void
    {
        $blocks = [
            Heading1::fromString("Shopping list"),
            Paragraph::fromString("My shopping list"),
            Heading2::fromString("Supermarket"),
            BulletedListItem::fromString("Tomato"),
            BulletedListItem::fromString("Onions"),
            BulletedListItem::fromString("Potato"),
            Heading2::fromString("Mall"),
            BulletedListItem::fromString("Black T-Shirt"),
        ];

        $markdown = MarkdownRenderer::render(...$blocks);

        $expected = <<<MARKDOWN
# Shopping list
My shopping list

## Supermarket
- Tomato
- Onions
- Potato
## Mall
- Black T-Shirt

MARKDOWN;

        $this->assertSame($expected, $markdown);
    }

    public function test_render_with_overrides(): void
    {
        $blocks = [
            Heading1::fromString("Post title"),
            Paragraph::fromString("My dummy post content."),
        ];

        $overrides = [
            BlockType::Heading1->value => new class implements \Notion\Blocks\Renderer\BlockRendererInterface {
                public static function render(\Notion\Blocks\BlockInterface $block, int $depth = 0): string
                {
                    $original = Heading1Renderer::render($block, $depth);
                    return "{$original} (OVERRIDE)";
                }
            },
        ];

        $markdown = MarkdownRenderer::renderWithOverrides($overrides, ...$blocks);

        $expected = <<<MARKDOWN
# Post title (OVERRIDE)
My dummy post content.


MARKDOWN;

        $this->assertSame($expected, $markdown);
    }
}
