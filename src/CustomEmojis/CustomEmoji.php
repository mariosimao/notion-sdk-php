<?php

namespace Notion\CustomEmojis;

/**
 * @psalm-type CustomEmojiJson = array{
 *      id: string,
 *      name: string,
 *      url: string,
 * }
 *
 * @psalm-immutable
 */
final readonly class CustomEmoji
{
    private function __construct(
        public string $id,
        public string $name,
        public string $url,
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

    /** @psalm-return CustomEmojiJson */
    public function toArray(): array
    {
        return [
            "id"   => $this->id,
            "name" => $this->name,
            "url"  => $this->url,
        ];
    }
}
