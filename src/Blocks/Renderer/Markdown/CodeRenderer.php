<?php

namespace Notion\Blocks\Renderer\Markdown;

use Notion\Blocks\BlockInterface;
use Notion\Blocks\Code;
use Notion\Blocks\Renderer\BlockRendererInterface;
use Notion\Blocks\Renderer\MarkdownRenderer;

final class CodeRenderer implements BlockRendererInterface
{
    public static function render(BlockInterface $block, int $depth = 0): string
    {
        if (!$block instanceof Code) {
            return "";
        }

        $language = $block->language->value;
        $code = RichTextRenderer::render(...$block->text);
        $markdown = "```{$language}\n{$code}\n```";

        return MarkdownRenderer::ident($markdown, $depth);
    }
}
