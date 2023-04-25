<?php

namespace Notion\Blocks\Renderer\Markdown;

use Notion\Blocks\BlockInterface;
use Notion\Blocks\Quote;
use Notion\Blocks\Renderer\BlockRendererInterface;
use Notion\Blocks\Renderer\MarkdownRenderer;

final class QuoteRenderer implements BlockRendererInterface
{
    public static function render(BlockInterface $block, int $depth = 0): string
    {
        if (!$block instanceof Quote) {
            return "";
        }

        $text = RichTextRenderer::render(...$block->text);

        $markdown = MarkdownRenderer::ident("> {$text}", $depth);

        foreach ($block->children as $child) {
            $markdown .= "\n>\n> " . MarkdownRenderer::renderBlock($child, $depth);
        }

        return $markdown;
    }
}
