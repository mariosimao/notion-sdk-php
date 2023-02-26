<?php

namespace Notion\Pages\Properties;

/**
 * @psalm-type PropertyMetadataJson = array{ id: string, type: string, ... }
 *
 * @psalm-immutable
 */
class PropertyMetadata
{
    private function __construct(
        public readonly string $id,
        public readonly PropertyType $type,
        private readonly string|null $unknownType = null,
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
        $type = PropertyType::tryFrom($array["type"]) ?? PropertyType::Unknown;

        return new self(
            $array["id"],
            $type,
            $type === PropertyType::Unknown ? $array["type"] : null,
        );
    }

    public function toArray(): array
    {
        $type = $this->type !== PropertyType::Unknown ? $this->type->value : $this->unknownType;

        return [
            "id"   => $this->id,
            "type" => $type,
        ];
    }
}
