<?php

namespace Notion\Blocks\Renderer\Markdown;

use Notion\Blocks\Audio;
use Notion\Blocks\BlockInterface;
use Notion\Blocks\Renderer\BlockRendererInterface;
use Notion\Blocks\Renderer\MarkdownRenderer;

final readonly class AudioRenderer implements BlockRendererInterface
{
    public static function render(BlockInterface $block, int $depth = 0): string
    {
        if (!$block instanceof Audio) {
            return "";
        }

        if ($block->file->url === null) {
            return "";
        }

        return MarkdownRenderer::ident($block->file->url, $depth);
    }
}
