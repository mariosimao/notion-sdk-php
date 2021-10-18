<?php

namespace Notion\Common;

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
}
