<?php

namespace Notion\Common;

/**
 * @psalm-type CustomEmojiJson = array{
 *      id: string,
 *      name?: string,
 *      url?: string,
 * }
 *
 * @psalm-immutable
 */
final readonly class CustomEmoji
{
    private function __construct(
        public string $id,
        public string|null $name = null,
        public string|null $url = null,
    ) {
    }

    public static function create(string $id, string|null $name = null, string|null $url = null): self
    {
        return new self($id, $name, $url);
    }

    /**
     * @psalm-param CustomEmojiJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        return new self(
            $array["id"],
            $array["name"] ?? null,
            $array["url"] ?? null,
        );
    }

    /** @return CustomEmojiJson */
    public function toArray(): array
    {
        $array = [ "id" => $this->id ];

        if ($this->name !== null) {
            $array["name"] = $this->name;
        }
        if ($this->url !== null) {
            $array["url"] = $this->url;
        }

        return $array;
    }
}
