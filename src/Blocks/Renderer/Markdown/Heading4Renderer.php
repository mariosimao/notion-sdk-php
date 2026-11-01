<?php

namespace Notion\Blocks\Renderer\Markdown;

use Notion\Blocks\BlockInterface;
use Notion\Blocks\Heading4;
use Notion\Blocks\Renderer\BlockRendererInterface;
use Notion\Blocks\Renderer\MarkdownRenderer;

final readonly class Heading4Renderer implements BlockRendererInterface
{
    public static function render(BlockInterface $block, int $depth = 0): string
    {
        if (!$block instanceof Heading4) {
            return "";
        }

        $main = RichTextRenderer::render(...$block->text);
        return MarkdownRenderer::ident("#### {$main}", $depth);
    }
}
