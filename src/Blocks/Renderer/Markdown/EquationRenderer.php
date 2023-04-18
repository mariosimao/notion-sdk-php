<?php

namespace Notion\Blocks\Renderer\Markdown;

use Notion\Blocks\BlockInterface;
use Notion\Blocks\EquationBlock;
use Notion\Blocks\Renderer\BlockRendererInterface;
use Notion\Blocks\Renderer\MarkdownRenderer;

final class EquationRenderer implements BlockRendererInterface
{
    public static function render(BlockInterface $block, int $depth = 0): string
    {
        if (!$block instanceof EquationBlock) {
            return "";
        }

        $equation = $block->equation->expression;
        return MarkdownRenderer::ident("$$ {$equation} $$", $depth);
    }
}
