<?php

namespace Notion\Webhooks;

/**
 * @psalm-type EntityJson = array{
 *     id: string,
 *     type: string,
 * }
 *
 * @psalm-immutable
 */
final readonly class Entity
{
    private function __construct(
        public string $id,
        public EntityType $type,
    ) {
    }

    /**
     * @psalm-param EntityJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        return new self($array["id"], EntityType::from($array["type"]));
    }
}
