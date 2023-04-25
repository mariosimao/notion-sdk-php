<?php

namespace Notion\Blocks\Renderer;

use Notion\Blocks\BlockInterface;
use Notion\Blocks\BlockType;
use Notion\Blocks\Renderer\Markdown;

class MarkdownRenderer implements RendererInterface
{
    public static function render(BlockInterface ...$blocks): string
    {
        $markdown = "";
        foreach ($blocks as $block) {
            $markdown = $markdown . self::renderBlock($block) . "\n";
        }

        return $markdown;
    }

    public static function renderBlock(BlockInterface $block, int $depth = 0): string
    {
        return match ($block->metadata()->type) {
            BlockType::Bookmark         => Markdown\BookmarkRenderer::render($block, $depth),
            BlockType::Breadcrumb       => Markdown\BreadcrumbRenderer::render($block, $depth),
            BlockType::BulletedListItem => Markdown\BulletedListItemRenderer::render($block, $depth),
            BlockType::Callout          => Markdown\CalloutRenderer::render($block, $depth),
            BlockType::ChildDatabase    => Markdown\ChildDatabaseRenderer::render($block, $depth),
            BlockType::ChildPage        => Markdown\ChildPageRenderer::render($block, $depth),
            BlockType::Code             => Markdown\CodeRenderer::render($block, $depth),
            BlockType::Column           => Markdown\ColumnRenderer::render($block, $depth),
            BlockType::ColumnList       => Markdown\ColumnListRenderer::render($block, $depth),
            BlockType::Divider          => Markdown\DividerRenderer::render($block, $depth),
            BlockType::Embed            => Markdown\EmbedRenderer::render($block, $depth),
            BlockType::Equation         => Markdown\EquationRenderer::render($block, $depth),
            BlockType::File             => Markdown\FileRenderer::render($block, $depth),
            BlockType::Heading1         => Markdown\Heading1Renderer::render($block, $depth),
            BlockType::Heading2         => Markdown\Heading2Renderer::render($block, $depth),
            BlockType::Heading3         => Markdown\Heading3Renderer::render($block, $depth),
            BlockType::Image            => Markdown\ImageRenderer::render($block, $depth),
            BlockType::LinkPreview      => Markdown\LinkPreviewRenderer::render($block, $depth),
            BlockType::NumberedListItem => Markdown\NumberedListItemRenderer::render($block, $depth),
            BlockType::Paragraph        => Markdown\ParagraphRenderer::render($block, $depth),
            BlockType::Pdf              => Markdown\PdfRenderer::render($block, $depth),
            BlockType::Quote            => Markdown\QuoteRenderer::render($block, $depth),
            BlockType::TableOfContents  => Markdown\TableOfContentsRenderer::render($block, $depth),
            BlockType::ToDo             => Markdown\ToDoRenderer::render($block, $depth),
            BlockType::Toggle           => Markdown\ToggleRenderer::render($block, $depth),
            BlockType::Video            => Markdown\VideoRenderer::render($block, $depth),
            default                     => "",
        };
    }

    public static function ident(string $text, int $depth): string
    {
        $lines = array_map(
            function (string $line) use ($depth): string {
                if (strlen($line) == 0) {
                    return $line;
                }

                $padding = str_repeat(" ", $depth * 2);
                return $padding . $line;
            },
            explode("\n", $text),
        );

        return implode("\n", $lines);
    }
}
