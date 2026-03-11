<?php

namespace Notion\Test\Unit\Blocks\Renderer\Markdown;

use Notion\Blocks\ChildDatabase;
use Notion\Blocks\Divider;
use Notion\Blocks\Renderer\Markdown\ChildDatabaseRenderer;
use PHPUnit\Framework\TestCase;

class ChildDatabaseRendererTest extends TestCase
{
    public function test_render(): void
    {
        $block = ChildDatabase::fromArray([
            "id"                => "abc123",
            "created_time"      => "2023-01-01 00:00:00",
            "last_edited_time"  => "2023-01-01 00:00:00",
            "archived"          => false,
            "has_children"      => false,
            "type"              => "child_database",
            "child_database"    => [
                "title" => "Database title"
            ],
        ]);

        $markdown = ChildDatabaseRenderer::render($block);

        $expected = "Database title";

        $this->assertSame($expected, $markdown);
    }


    public function test_invalid_block(): void
    {
        $markdown = ChildDatabaseRenderer::render(Divider::create());

        $this->assertSame("", $markdown);
    }
}
