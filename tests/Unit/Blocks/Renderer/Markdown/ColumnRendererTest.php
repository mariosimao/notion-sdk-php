<?php

namespace Notion\Test\Unit\Blocks\Renderer\Markdown;

use Notion\Blocks\Column;
use Notion\Blocks\ColumnList;
use Notion\Blocks\Divider;
use Notion\Blocks\Paragraph;
use Notion\Blocks\Renderer\Markdown\ColumnListRenderer;
use Notion\Blocks\Renderer\Markdown\ColumnRenderer;
use PHPUnit\Framework\TestCase;

class ColumnRendererTest extends TestCase
{
    public function test_render(): void
    {
        $col1 = Column::create(
            Paragraph::fromString("Text 1"),
            Paragraph::fromString("Text 2"),
        );
        $col2 = Column::create(
            Paragraph::fromString("Text 3"),
        );

        $columns = ColumnList::create($col1, $col2);

        $markdown = ColumnListRenderer::render($columns);

        $expected = <<<MARKDOWN
Text 1


Text 2


Text 3

MARKDOWN;

        $this->assertSame($expected, $markdown);
    }


    public function test_invalid_block(): void
    {
        $markdown = ColumnRenderer::render(Divider::create());

        $this->assertSame("", $markdown);
    }

    public function test_invalid_block_column_list(): void
    {
        $markdown = ColumnListRenderer::render(Divider::create());

        $this->assertSame("", $markdown);
    }
}
