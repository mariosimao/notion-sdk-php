<?php

namespace Notion\Pages\Markdown;

/** @psalm-immutable */
final readonly class ContentUpdate
{
    private function __construct(
        public string $oldStr,
        public string $newStr,
        public bool $replaceAllMatches,
    ) {
    }

    public static function create(string $oldStr, string $newStr): self
    {
        return new self($oldStr, $newStr, false);
    }

    public function replaceAllMatches(bool $replaceAll = true): self
    {
        return new self($this->oldStr, $this->newStr, $replaceAll);
    }

    /**
     * @return array{ old_str: string, new_str: string, replace_all_matches: bool }
     *
     * @internal
     */
    public function toArray(): array
    {
        return [
            "old_str" => $this->oldStr,
            "new_str" => $this->newStr,
            "replace_all_matches" => $this->replaceAllMatches,
        ];
    }
}
