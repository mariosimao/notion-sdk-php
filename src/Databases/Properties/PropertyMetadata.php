<?php

namespace Notion\Databases\Properties;

/**
 * @psalm-type PropertyMetadataJson = array{ id: string, name: string, type: string }
 *
 * @psalm-immutable
 */
class PropertyMetadata
{
    private function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly PropertyType $type,
    ) {
    }

    public static function create(string $id, string $name, PropertyType $type): self
    {
        return new self($id, $name, $type);
    }

    /**
     * @param PropertyMetadataJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        return new self($array["id"], $array["name"], PropertyType::from($array["type"]));
    }

    public function toArray(): array
    {
        return [
            "id"   => $this->id,
            "name" => $this->name,
            "type" => $this->type->value,
        ];
    }
}
