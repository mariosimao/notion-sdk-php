<?php

namespace Notion\Authentication;

/**
 * @psalm-type ExternalAccountJson = array{
 *     key: string,
 *     name: string,
 * }
 *
 * @psalm-immutable
 */
final readonly class ExternalAccount
{
    private function __construct(
        public string $key,
        public string $name,
    ) {
    }

    public static function create(string $key, string $name): self
    {
        return new self($key, $name);
    }

    /**
     * @psalm-param ExternalAccountJson $array
     */
    public static function fromArray(array $array): self
    {
        return new self(
            key: $array["key"],
            name: $array["name"],
        );
    }

    /**
     * @psalm-return ExternalAccountJson
     */
    public function toArray(): array
    {
        return [
            "key" => $this->key,
            "name" => $this->name,
        ];
    }
}
