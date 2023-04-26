<?php

namespace Notion\Blocks\Renderer\Markdown;

use Notion\Blocks\BlockInterface;
use Notion\Blocks\Pdf;
use Notion\Blocks\Renderer\BlockRendererInterface;
use Notion\Blocks\Renderer\MarkdownRenderer;

final class PdfRenderer implements BlockRendererInterface
{
    public static function render(BlockInterface $block, int $depth = 0): string
    {
        if (!$block instanceof Pdf) {
            return "";
        }

        return MarkdownRenderer::ident($block->file->url, $depth);
    }
}
