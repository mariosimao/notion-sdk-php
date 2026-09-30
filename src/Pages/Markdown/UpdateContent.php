<?php

namespace Notion\Pages\Markdown;

/** @psalm-immutable */
final readonly class UpdateContent implements MarkdownUpdateInterface
{
    /** @param list<ContentUpdate> $contentUpdates */
    private function __construct(
        public array $contentUpdates,
        public bool $allowDeletingContent,
    ) {
    }

    public static function create(ContentUpdate ...$contentUpdates): self
    {
        return new self(array_values($contentUpdates), false);
    }

    public function addUpdate(ContentUpdate $contentUpdate): self
    {
        return new self([ ...$this->contentUpdates, $contentUpdate ], $this->allowDeletingContent);
    }

    public function allowDeletingContent(bool $allow = true): self
    {
        return new self($this->contentUpdates, $allow);
    }

    public function toArray(): array
    {
        return [
            "type" => "update_content",
            "update_content" => [
                "content_updates" => array_map(fn(ContentUpdate $u) => $u->toArray(), $this->contentUpdates),
                "allow_deleting_content" => $this->allowDeletingContent,
            ],
        ];
    }
}
