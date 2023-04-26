<?php

namespace Notion\Test\Unit\Blocks\Renderer\Markdown;

use Notion\Blocks\BulletedListItem;
use Notion\Blocks\Divider;
use Notion\Blocks\Renderer\Markdown\BulletedListItemRenderer;
use PHPUnit\Framework\TestCase;

class BulletedListItemRendererTest extends TestCase
{
    public function test_render(): void
    {
        $block = BulletedListItem::fromString("Item 1")
            ->addChild(BulletedListItem::fromString("Item 2"))
            ->addChild(BulletedListItem::fromString("Item 3")
                ->addChild(BulletedListItem::fromString("Item 4")));

        $markdown = BulletedListItemRenderer::render($block);

        $expected = <<<MARKDOWN
- Item 1
  - Item 2
  - Item 3
    - Item 4
MARKDOWN;

        $this->assertSame($expected, $markdown);
    }


    public function test_invalid_block(): void
    {
        $markdown = BulletedListItemRenderer::render(Divider::create());

        $this->assertSame("", $markdown);
    }
}
