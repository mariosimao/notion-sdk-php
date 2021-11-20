<?php

namespace Notion\Common;

/**
 * @psalm-type EmojiJson = array{ type: "emoji", emoji: string }
 *
 * @psalm-immutable
 */
class Emoji
{
    private string $emoji;

    private function __construct(string $emoji)
    {
        $this->emoji = $emoji;
    }

    public static function create(string $emoji): self
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

    public function emoji(): string
    {
        return $this->emoji;
    }
}
