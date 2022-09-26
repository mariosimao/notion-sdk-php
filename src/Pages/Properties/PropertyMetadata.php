<?php

namespace Notion\Pages\Properties;

/**
 * @psalm-type PropertyMetadataJson = array{ id: string, type: string }
 *
 * @psalm-immutable
 */
class PropertyMetadata
{
    private function __construct(
        public readonly string $id,
        public readonly PropertyType $type,
    ) {
    }

    /** @psalm-mutation-free */
    public static function create(string $id, PropertyType $type): self
    {
        return new self($id, $type);
    }

    /**
     * @param PropertyMetadataJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        return new self($array["id"], PropertyType::from($array["type"]));
    }

    public function toArray(): array
    {
        return [
            "id"   => $this->id,
            "type" => $this->type->value,
        ];
    }
}
