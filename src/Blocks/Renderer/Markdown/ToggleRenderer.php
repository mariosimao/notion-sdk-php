<?php

namespace Notion\Blocks\Renderer\Markdown;

use Notion\Blocks\BlockInterface;
use Notion\Blocks\Toggle;
use Notion\Blocks\Renderer\BlockRendererInterface;
use Notion\Blocks\Renderer\MarkdownRenderer;
use Notion\Common\RichText;

final class ToggleRenderer implements BlockRendererInterface
{
    public static function render(BlockInterface $block, int $depth = 0): string
    {
        if (!$block instanceof Toggle) {
            return "";
        }

        $text = RichText::multipleToString(...$block->text);

        $markdown = MarkdownRenderer::ident("<details>", $depth);

        $markdown .= MarkdownRenderer::ident("\n<summary>{$text}</summary>", $depth);
        foreach ($block->children as $child) {
            $markdown .= "\n\n" . MarkdownRenderer::renderBlock($child, $depth);
        }

        $markdown .= MarkdownRenderer::ident("</details>", $depth);

        return $markdown;
    }
}
