<?php

namespace Notion\Databases\Properties;

/**
 * @psalm-type PropertyMetadataJson = array{ id: string, name: string, type: string, description?: string, ... }
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
        public readonly string|null $description = null,
    ) {
    }

    public static function create(string $id, string $name, PropertyType $type, ?string $description = null): self
    {
        return new self($id, $name, $type, description: $description);
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
            $array["description"] ?? null,
        );
    }

    public function toArray(): array
    {
        $type = $this->type !== PropertyType::Unknown ? $this->type->value : $this->unknownType;

        return [
            "id"   => $this->id,
            "name" => $this->name,
            "type" => $type,
            ...($this->description !== null ? [
                "description" => $this->description,
            ] : []),
        ];
    }
}
