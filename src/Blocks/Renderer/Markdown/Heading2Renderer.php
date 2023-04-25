<?php

namespace Notion\Blocks\Renderer\Markdown;

use Notion\Blocks\BlockInterface;
use Notion\Blocks\Heading2;
use Notion\Blocks\Renderer\BlockRendererInterface;
use Notion\Blocks\Renderer\MarkdownRenderer;

final class Heading2Renderer implements BlockRendererInterface
{
    public static function render(BlockInterface $block, int $depth = 0): string
    {
        if (!$block instanceof Heading2) {
            return "";
        }

        $main = RichTextRenderer::render(...$block->text);
        return MarkdownRenderer::ident("## {$main}", $depth);
    }
}
