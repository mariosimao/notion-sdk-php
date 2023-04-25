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
                $markdown = self::around($markdown, "**");
            }
            if ($t->annotations->isItalic) {
                $markdown = self::around($markdown, "*");
            }
            if ($t->annotations->isStrikeThrough) {
                $markdown = self::around($markdown, "~~");
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

    private static function around(string $text, string $around): string
    {
        preg_match("/^(\s*)/", $text, $leftSpace);
        preg_match("/(\s*)$/", $text, $righSpace);

        $text = trim($text);

        return "{$leftSpace[0]}{$around}{$text}{$around}{$righSpace[0]}";
    }
}
