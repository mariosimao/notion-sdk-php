<?php

namespace Notion\Blocks\Renderer\Markdown;

use Notion\Blocks\BlockInterface;
use Notion\Blocks\NumberedListItem;
use Notion\Blocks\Renderer\BlockRendererInterface;
use Notion\Blocks\Renderer\MarkdownRenderer;

final readonly class NumberedListItemRenderer implements BlockRendererInterface
{
    public static function render(BlockInterface $block, int $depth = 0): string
    {
        if (!$block instanceof NumberedListItem) {
            return "";
        }

        $main = RichTextRenderer::render(...$block->text);
        $number = $block->listStartIndex ?? 1;
        $markdown = MarkdownRenderer::ident("{$number}. {$main}", $depth);

        foreach ($block->children as $child) {
            $markdown .= "\n" . MarkdownRenderer::renderBlock($child, $depth + 1);
        }

        return $markdown;
    }
}
