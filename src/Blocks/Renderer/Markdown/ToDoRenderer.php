<?php

namespace Notion\Blocks\Renderer\Markdown;

use Notion\Blocks\BlockInterface;
use Notion\Blocks\ToDo;
use Notion\Blocks\Renderer\BlockRendererInterface;
use Notion\Blocks\Renderer\MarkdownRenderer;

final class ToDoRenderer implements BlockRendererInterface
{
    public static function render(BlockInterface $block, int $depth = 0): string
    {
        if (!$block instanceof ToDo) {
            return "";
        }

        $text = RichTextRenderer::render(...$block->text);
        $check = $block->checked ? "x" : " ";
        $markdown = MarkdownRenderer::ident("- [{$check}] {$text}", $depth);

        foreach ($block->children as $child) {
            $markdown .= "\n" . MarkdownRenderer::renderBlock($child, $depth + 1);
        }

        return $markdown;
    }
}
