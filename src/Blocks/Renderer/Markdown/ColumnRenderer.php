<?php

namespace Notion\Blocks\Renderer\Markdown;

use Notion\Blocks\BlockInterface;
use Notion\Blocks\Column;
use Notion\Blocks\Renderer\BlockRendererInterface;
use Notion\Blocks\Renderer\MarkdownRenderer;

final class ColumnRenderer implements BlockRendererInterface
{
    public static function render(BlockInterface $block, int $depth = 0): string
    {
        if (!$block instanceof Column) {
            return "";
        }

        $markdown = "";
        $isFirst = true;
        foreach ($block->children as $child) {
            $newLine = $isFirst ? "" : "\n\n";
            $markdown .= $newLine . MarkdownRenderer::renderBlock($child, $depth);
            $isFirst = false;
        }

        return $markdown;
    }
}
