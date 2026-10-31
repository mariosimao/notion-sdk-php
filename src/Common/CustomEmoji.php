<?php

namespace Notion\Common;

/**
 * @psalm-type CustomEmojiJson = array{
 *      type: "custom_emoji",
 *      custom_emoji: array{ id: string, name?: string, url?: string },
 * }
 *
 * @psalm-immutable
 */
final readonly class CustomEmoji
{
    private function __construct(
        public string $id,
        public string|null $name,
        public string|null $url,
    ) {
    }

    public static function fromId(string $id): self
    {
        return new self($id, null, null);
    }

    /**
     * @param CustomEmojiJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        $customEmoji = $array["custom_emoji"];

        return new self(
            $customEmoji["id"],
            $customEmoji["name"] ?? null,
            $customEmoji["url"] ?? null,
        );
    }

    /**
     * Only the ID is serialized, as `name` and `url` are read-only and
     * Notion identifies custom emojis by ID on writes.
     */
    public function toArray(): array
    {
        return [
            "type"         => "custom_emoji",
            "custom_emoji" => [ "id" => $this->id ],
        ];
    }
}
