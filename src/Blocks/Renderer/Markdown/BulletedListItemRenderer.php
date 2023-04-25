<?php

namespace Notion\Blocks\Renderer\Markdown;

use Notion\Blocks\BlockInterface;
use Notion\Blocks\BulletedListItem;
use Notion\Blocks\Renderer\BlockRendererInterface;
use Notion\Blocks\Renderer\MarkdownRenderer;

final class BulletedListItemRenderer implements BlockRendererInterface
{
    public static function render(BlockInterface $block, int $depth = 0): string
    {
        if (!$block instanceof BulletedListItem) {
            return "";
        }

        $main = RichTextRenderer::render(...$block->text);
        $markdown = MarkdownRenderer::ident("- {$main}", $depth);

        foreach ($block->children as $child) {
            $markdown .= "\n" . MarkdownRenderer::renderBlock($child, $depth + 1);
        }

        return $markdown;
    }
}
