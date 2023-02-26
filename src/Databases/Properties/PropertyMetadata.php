<?php

namespace Notion\Databases\Properties;

/**
 * @psalm-type PropertyMetadataJson = array{ id: string, name: string, type: string, ... }
 *
 * @psalm-immutable
 */
class PropertyMetadata
{
    private function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly PropertyType $type,
        private readonly string|null $unknownType = null,
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
        $type = PropertyType::tryFrom($array["type"]) ?? PropertyType::Unknown;

        return new self(
            $array["id"],
            $array["name"],
            $type,
            $type === PropertyType::Unknown ? $array["type"] : null,
        );
    }

    public function toArray(): array
    {
        $type = $this->type !== PropertyType::Unknown ? $this->type->value : $this->unknownType;

        return [
            "id"   => $this->id,
            "name" => $this->name,
            "type" => $type,
        ];
    }
}
