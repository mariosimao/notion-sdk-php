<?php

namespace Notion\Test\Unit\Blocks\Renderer;

use Notion\Blocks\BulletedListItem;
use Notion\Blocks\Heading1;
use Notion\Blocks\Heading2;
use Notion\Blocks\Paragraph;
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
}
