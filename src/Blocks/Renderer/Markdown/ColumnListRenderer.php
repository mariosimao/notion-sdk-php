<?php

namespace Notion\Blocks\Renderer\Markdown;

use Notion\Blocks\BlockInterface;
use Notion\Blocks\ColumnList;
use Notion\Blocks\Renderer\BlockRendererInterface;
use Notion\Blocks\Renderer\MarkdownRenderer;

final class ColumnListRenderer implements BlockRendererInterface
{
    public static function render(BlockInterface $block, int $depth = 0): string
    {
        if (!$block instanceof ColumnList) {
            return "";
        }

        $markdown = "";
        $isFirst = true;
        foreach ($block->columns as $child) {
            $newLine = $isFirst ? "" : "\n\n";
            $markdown .= $newLine . MarkdownRenderer::renderBlock($child, $depth);
            $isFirst = false;
        }

        return $markdown;
    }
}
