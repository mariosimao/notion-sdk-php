<?php

namespace Notion\Comments;

use Notion\Common\MarkdownVersion;
use Notion\Common\RichText;

/**
 * Content of a comment, either as rich text or as a markdown string.
 *
 * Exactly one representation is used when creating or updating a comment.

 */
final readonly class CommentBody
{
    /** @param RichText[] $richText */
    private function __construct(
        public array $richText,
        public string|null $markdown,
        public MarkdownVersion|null $markdownVersion,
    ) {
    }

    public static function richText(RichText ...$text): self
    {
        return new self($text, null, null);
    }

    /**
     * Markdown body. Comments support inline formatting only (bold, italic,
     * strikethrough, inline code, links), inline equations and mentions.
     */
    public static function markdown(
        string $markdown,
        MarkdownVersion $version = MarkdownVersion::V2,
    ): self {
        return new self([], $markdown, $version);
    }

    public function isMarkdown(): bool
    {
        return $this->markdown !== null;
    }

    public function isRichText(): bool
    {
        return $this->markdown === null;
    }

    public function toArray(): array
    {
        if ($this->markdown !== null) {
            $array = [ "markdown" => $this->markdown ];
            if ($this->markdownVersion !== null) {
                $array["markdown_version"] = $this->markdownVersion->value;
            }

            return $array;
        }

        return [
            "rich_text" => array_map(fn (RichText $t) => $t->toArray(), $this->richText),
        ];
    }
}
