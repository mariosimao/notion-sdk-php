<?php

namespace Notion\Pages\Markdown;

/** @psalm-immutable */
final readonly class ReplaceContentRange implements MarkdownUpdateInterface
{
    private function __construct(
        public string $contentRange,
        public string $content,
        public bool $allowDeletingContent,
    ) {
    }

    /** @param string $contentRange Ellipsis-based selection, e.g. "start text...end text". */
    public static function create(string $contentRange, string $content): self
    {
        return new self($contentRange, $content, false);
    }

    public function allowDeletingContent(bool $allow = true): self
    {
        return new self($this->contentRange, $this->content, $allow);
    }

    public function toArray(): array
    {
        return [
            "type" => "replace_content_range",
            "replace_content_range" => [
                "content" => $this->content,
                "content_range" => $this->contentRange,
                "allow_deleting_content" => $this->allowDeletingContent,
            ],
        ];
    }
}
