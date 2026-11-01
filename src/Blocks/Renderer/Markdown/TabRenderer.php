<?php

namespace Notion\Blocks\Renderer\Markdown;

use Notion\Blocks\BlockInterface;
use Notion\Blocks\Renderer\BlockRendererInterface;
use Notion\Blocks\Renderer\MarkdownRenderer;
use Notion\Blocks\Tab;

final readonly class TabRenderer implements BlockRendererInterface
{
    public static function render(BlockInterface $block, int $depth = 0): string
    {
        if (!$block instanceof Tab) {
            return "";
        }

        $sections = [];
        foreach ($block->tabs as $tab) {
            $icon = $tab->icon;
            $emoji = ($icon !== null && $icon->isEmoji()) ? $icon->emoji->toString() . " " : "";
            $label = RichTextRenderer::render(...$tab->text);

            $section = MarkdownRenderer::ident("**{$emoji}{$label}**", $depth);
            foreach ($tab->children as $child) {
                $section .= "\n\n" . rtrim(MarkdownRenderer::renderBlock($child, $depth), "\n");
            }

            $sections[] = $section;
        }

        return implode("\n\n", $sections) . "\n";
    }
}
