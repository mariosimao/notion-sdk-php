<?php

namespace Notion\Webhooks;

/**
 * @psalm-type AuthorJson = array{
 *     id: string,
 *     type: string,
 * }
 *
 * @psalm-immutable
 */
final readonly class Author
{
    private function __construct(
        public string $id,
        public AuthorType $type,
    ) {
    }

    /**
     * @psalm-param AuthorJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        return new self($array["id"], AuthorType::from($array["type"]));
    }
}
