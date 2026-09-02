<?php

namespace Notion\Common;

/**
 * @psalm-type CustomEmojiJson = array{ id: string, name: string, url: string }
 *
 * @psalm-immutable
 */
class CustomEmoji
{
    private function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $url,
    ) {
    }

    /**
     * @psalm-param CustomEmojiJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        return new self($array["id"], $array["name"], $array["url"]);
    }

    public function toArray(): array
    {
        return [
            "id"   => $this->id,
            "name" => $this->name,
            "url"  => $this->url,
        ];
    }
}
