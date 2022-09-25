<?php

namespace Notion\Common;

class Icon
{
    private function __construct(
        public readonly Emoji|null $emoji,
        public readonly File|null $file,
    ) {
        if ($emoji === null && $file === null) {
            throw new \Exception("Icon must be either emoji or file, not both null.");
        }

        if ($emoji !== null && $file !== null) {
            throw new \Exception("Icon must be either emoji or file, not both.");
        }
    }

    public static function fromEmoji(Emoji $emoji): self
    {
        return new self($emoji, null);
    }

    public static function fromFile(File $file): self
    {
        return new self(null, $file);
    }

    /** @psalm-mutation-free */
    public function toArray(): array
    {
        if ($this->emoji !== null) {
            return $this->emoji->toArray();
        }

        if ($this->file !== null) {
            return $this->file->toArray();
        }

        return [];
    }

    /**
     * @psalm-assert-if-true Emoji $this->emoji
     */
    public function isEmoji(): bool
    {
        return $this->emoji !== null;
    }

    /**
     * @psalm-assert-if-true File $this->file
     */
    public function isFile(): bool
    {
        return $this->file !== null;
    }
}
