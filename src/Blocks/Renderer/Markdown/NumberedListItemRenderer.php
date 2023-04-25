<?php

namespace Notion\Blocks\Renderer\Markdown;

use Notion\Blocks\BlockInterface;
use Notion\Blocks\NumberedListItem;
use Notion\Blocks\Renderer\BlockRendererInterface;
use Notion\Blocks\Renderer\MarkdownRenderer;

final class NumberedListItemRenderer implements BlockRendererInterface
{
    public static function render(BlockInterface $block, int $depth = 0): string
    {
        if (!$block instanceof NumberedListItem) {
            return "";
        }

        $main = RichTextRenderer::render(...$block->text);
        $markdown = MarkdownRenderer::ident("1. {$main}", $depth);

        foreach ($block->children as $child) {
            $markdown .= "\n" . MarkdownRenderer::renderBlock($child, $depth + 1);
        }

        return $markdown;
    }
}
