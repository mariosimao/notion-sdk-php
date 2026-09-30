<?php

namespace Notion\Pages\Markdown;

/** @psalm-immutable */
final readonly class InsertContent implements MarkdownUpdateInterface
{
    private function __construct(
        public string $content,
        public string|null $after,
        public InsertPosition|null $position,
    ) {
    }

    /** Appends content to the end of the page. */
    public static function create(string $content): self
    {
        return new self($content, null, null);
    }

    public static function atStart(string $content): self
    {
        return new self($content, null, InsertPosition::Start);
    }

    public static function atEnd(string $content): self
    {
        return new self($content, null, InsertPosition::End);
    }

    /** @param string $selection Ellipsis-based selection, e.g. "start text...end text". */
    public static function after(string $selection, string $content): self
    {
        return new self($content, $selection, null);
    }

    public function toArray(): array
    {
        $insert = [ "content" => $this->content ];
        if ($this->after !== null) {
            $insert["after"] = $this->after;
        }
        if ($this->position !== null) {
            $insert["position"] = [ "type" => $this->position->value ];
        }

        return [
            "type" => "insert_content",
            "insert_content" => $insert,
        ];
    }
}
