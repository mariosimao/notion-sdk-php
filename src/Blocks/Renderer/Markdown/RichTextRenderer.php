<?php

namespace Notion\Blocks\Renderer\Markdown;

use Notion\Common\RichText;

final class RichTextRenderer
{
    public static function render(RichText ...$text): string
    {
        $result = "";
        foreach ($text as $t) {
            $markdown = $t->plainText;

            if ($t->isEquation()) {
                $markdown = "\${$markdown}\$";
            }
            if ($t->annotations->isCode) {
                $markdown = "`{$markdown}`";
            }
            if ($t->annotations->isBold) {
                $markdown = "**{$markdown}**";
            }
            if ($t->annotations->isItalic) {
                $markdown = "*{$markdown}*";
            }
            if ($t->annotations->isStrikeThrough) {
                $markdown = "~~{$markdown}~~";
            }
            if ($t->annotations->isUnderline) {
                $markdown = "<u>{$markdown}</u>";
            }

            if ($t->href !== null) {
                $markdown = "[{$markdown}]({$t->href})";
            }

            $result = "{$result}{$markdown}";
        }

        return $result;
    }
}
