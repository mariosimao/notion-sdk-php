<?php

namespace Notion\Pages\Markdown;

/** @psalm-immutable */
final readonly class ReplaceContent implements MarkdownUpdateInterface
{
    private function __construct(
        public string $newStr,
        public bool $allowDeletingContent,
    ) {
    }

    public static function create(string $newStr): self
    {
        return new self($newStr, false);
    }

    public function allowDeletingContent(bool $allow = true): self
    {
        return new self($this->newStr, $allow);
    }

    public function toArray(): array
    {
        return [
            "type" => "replace_content",
            "replace_content" => [
                "new_str" => $this->newStr,
                "allow_deleting_content" => $this->allowDeletingContent,
            ],
        ];
    }
}
