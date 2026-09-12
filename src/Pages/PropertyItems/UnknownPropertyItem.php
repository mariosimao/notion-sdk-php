<?php

namespace Notion\Pages\PropertyItems;

use Notion\Pages\Properties\PropertyType;

/**
 * @psalm-type UnknownItemJson = array{
 *      id: string,
 *      type: string,
 *      ...
 * }
 *
 * @psalm-immutable
 */
final readonly class UnknownPropertyItem implements PropertyItemInterface
{
    private function __construct(
        private PropertyItemMetadata $metadata,
        private array $data,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function create(string $id, string $type, array $data = []): self
    {
        $metadata = PropertyItemMetadata::create($id, PropertyType::Unknown);

        return new self($metadata, $data);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var UnknownItemJson $array */
        $metadata = PropertyItemMetadata::fromArray($array);

        return new self($metadata, $array);
    }

    public function toArray(): array
    {
        return $this->data;
    }

    public function metadata(): PropertyItemMetadata
    {
        return $this->metadata;
    }
}
