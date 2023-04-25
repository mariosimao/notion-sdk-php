<?php

namespace Notion\Test\Unit\Blocks\Renderer\Markdown;

use Notion\Blocks\NumberedListItem;
use Notion\Blocks\Divider;
use Notion\Blocks\Renderer\Markdown\NumberedListItemRenderer;
use PHPUnit\Framework\TestCase;

class NumberedListItemRendererTest extends TestCase
{
    public function test_render(): void
    {
        $block = NumberedListItem::fromString("Item 1")
            ->addChild(NumberedListItem::fromString("Item 2"))
            ->addChild(NumberedListItem::fromString("Item 3")
                ->addChild(NumberedListItem::fromString("Item 4")));

        $markdown = NumberedListItemRenderer::render($block);

        $expected = <<<MARKDOWN
1. Item 1
  1. Item 2
  1. Item 3
    1. Item 4
MARKDOWN;

        $this->assertSame($expected, $markdown);
    }


    public function test_invalid_block(): void
    {
        $markdown = NumberedListItemRenderer::render(Divider::create());

        $this->assertSame("", $markdown);
    }
}
