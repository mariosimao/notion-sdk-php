<?php

namespace Notion\Common;

/**
 * @psalm-type EmojiJson = array{ type: "emoji", emoji: string }
 *
 * @psalm-immutable
 */
class Emoji
{
    private function __construct(
        public readonly string $emoji,
    ) {
    }

    public static function fromString(string $emoji): self
    {
        return new self($emoji);
    }

    /** @param EmojiJson $array */
    public static function fromArray(array $array): self
    {
        return new self($array["emoji"]);
    }

    public function toArray(): array
    {
        return [
            "type"  => "emoji",
            "emoji" => $this->emoji,
        ];
    }

    public function toString(): string
    {
        return $this->emoji;
    }
}
