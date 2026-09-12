<?php

namespace Notion\Pages\PropertyItems;

use Notion\Pages\Properties\PropertyType;

/**
 * @psalm-type PropertyItemMetadataJson = array{
 *     id: string,
 *     type: string,
 *     object?: string,
 *     next_url?: string|null,
 *     ...
 * }
 *
 * @psalm-immutable
 */
final readonly class PropertyItemMetadata
{
    private function __construct(
        public string $id,
        public PropertyType $type,
        public string|null $nextUrl = null,
        private string|null $unknownType = null,
    ) {
    }

    /** @psalm-mutation-free */
    public static function create(string $id, PropertyType $type, string|null $nextUrl = null): self
    {
        return new self($id, $type, $nextUrl);
    }

    /**
     * @param PropertyItemMetadataJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        $type = PropertyType::tryFrom($array["type"]) ?? PropertyType::Unknown;
        $nextUrl = $array["next_url"] ?? null;

        return new self(
            $array["id"],
            $type,
            $nextUrl,
            $type === PropertyType::Unknown ? $array["type"] : null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $type = $this->type !== PropertyType::Unknown ? $this->type->value : $this->unknownType;

        $array = [
            "object" => "property_item",
            "id"     => $this->id,
            "type"   => $type,
        ];

        if ($this->nextUrl !== null) {
            $array["next_url"] = $this->nextUrl;
        }

        return $array;
    }
}
