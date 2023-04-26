<?php

namespace Notion\Blocks\Renderer\Markdown;

use Notion\Blocks\BlockInterface;
use Notion\Blocks\Paragraph;
use Notion\Blocks\Renderer\BlockRendererInterface;
use Notion\Blocks\Renderer\MarkdownRenderer;

final class ParagraphRenderer implements BlockRendererInterface
{
    public static function render(BlockInterface $block, int $depth = 0): string
    {
        if (!$block instanceof Paragraph) {
            return "";
        }

        $text = RichTextRenderer::render(...$block->text);
        $markdown = MarkdownRenderer::ident($text . "\n", $depth);

        foreach ($block->children as $child) {
            $markdown .= "\n\n" . MarkdownRenderer::renderBlock($child, $depth + 1);
        }

        return $markdown;
    }
}
