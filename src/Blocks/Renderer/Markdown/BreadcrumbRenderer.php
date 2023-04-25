<?php

namespace Notion\Blocks\Renderer\Markdown;

use Notion\Blocks\BlockInterface;
use Notion\Blocks\Breadcrumb;
use Notion\Blocks\Renderer\BlockRendererInterface;
use Notion\Blocks\Renderer\MarkdownRenderer;

final class BreadcrumbRenderer implements BlockRendererInterface
{
    public static function render(BlockInterface $block, int $depth = 0): string
    {
        if (!$block instanceof Breadcrumb) {
            return "";
        }

        return MarkdownRenderer::ident("[Breadcrumb]", $depth);
    }
}
