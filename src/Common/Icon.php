<?php

namespace Notion\Common;

use Notion\Exceptions\IconException;

class Icon
{
    private function __construct(
        public readonly Emoji|null $emoji,
        public readonly File|null $file,
    ) {
        if ($emoji === null && $file === null) {
            throw IconException::bothNull();
        }

        if ($emoji !== null && $file !== null) {
            throw IconException::bothSet();
        }
    }

    /** @psalm-mutation-free */
    public static function fromEmoji(Emoji $emoji): self
    {
        return new self($emoji, null);
    }

    /** @psalm-mutation-free */
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
