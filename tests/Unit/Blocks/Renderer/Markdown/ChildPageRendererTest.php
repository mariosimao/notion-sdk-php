<?php

namespace Notion\Test\Unit\Blocks\Renderer\Markdown;

use Notion\Blocks\ChildPage;
use Notion\Blocks\Divider;
use Notion\Blocks\Renderer\Markdown\ChildPageRenderer;
use PHPUnit\Framework\TestCase;

class ChildPageRendererTest extends TestCase
{
    public function test_render(): void
    {
        $block = ChildPage::fromArray([
            "id"                => "abc123",
            "created_time"      => "2023-01-01 00:00:00",
            "last_edited_time"  => "2023-01-01 00:00:00",
            "archived"          => false,
            "has_children"      => false,
            "type"              => "child_page",
            "child_page"    => [
                "title" => "Page title"
            ],
        ]);

        $markdown = ChildPageRenderer::render($block);

        $expected = "Page title";

        $this->assertSame($expected, $markdown);
    }


    public function test_invalid_block(): void
    {
        $markdown = ChildPageRenderer::render(Divider::create());

        $this->assertSame("", $markdown);
    }
}
