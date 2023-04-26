<?php

namespace Notion\Test\Unit\Blocks\Renderer\Markdown;

use Notion\Blocks\Divider;
use Notion\Blocks\ToDo;
use Notion\Blocks\Renderer\Markdown\ToDoRenderer;
use PHPUnit\Framework\TestCase;

class ToDoRendererTest extends TestCase
{
    public function test_render(): void
    {
        $block = ToDo::fromString("Item 1")
            ->addChild(ToDo::fromString("Item 2")->check())
            ->addChild(ToDo::fromString("Item 3")
                ->addChild(ToDo::fromString("Item 4")));

        $markdown = ToDoRenderer::render($block);

        $expected = <<<MARKDOWN
- [ ] Item 1
  - [x] Item 2
  - [ ] Item 3
    - [ ] Item 4
MARKDOWN;

        $this->assertSame($expected, $markdown);
    }

    public function test_invalid_block(): void
    {
        $markdown = ToDoRenderer::render(Divider::create());

        $this->assertSame("", $markdown);
    }
}
