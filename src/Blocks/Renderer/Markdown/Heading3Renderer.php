<?php

namespace Notion\Blocks\Renderer\Markdown;

use Notion\Blocks\BlockInterface;
use Notion\Blocks\Heading3;
use Notion\Blocks\Renderer\BlockRendererInterface;
use Notion\Blocks\Renderer\MarkdownRenderer;

final class Heading3Renderer implements BlockRendererInterface
{
    public static function render(BlockInterface $block, int $depth = 0): string
    {
        if (!$block instanceof Heading3) {
            return "";
        }

        $main = RichTextRenderer::render(...$block->text);
        return MarkdownRenderer::ident("### {$main}", $depth);
    }
}
