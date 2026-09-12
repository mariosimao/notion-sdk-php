<?php

namespace Notion\Pages\PropertyItems;

use Notion\Pages\Properties\PropertyType;

/**
 * @psalm-type UniqueIdItemJson = array{
 *      id: string,
 *      type: "unique_id",
 *      unique_id: array{
 *          number: int,
 *          prefix: string|null,
 *      },
 *      ...
 * }
 *
 * @psalm-immutable
 */
final readonly class UniqueIdPropertyItem implements PropertyItemInterface
{
    private function __construct(
        private PropertyItemMetadata $metadata,
        public int $number,
        public string|null $prefix,
    ) {
    }

    public static function create(int $number, string|null $prefix = null, string $id = ""): self
    {
        $metadata = PropertyItemMetadata::create($id, PropertyType::UniqueId);

        return new self($metadata, $number, $prefix);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var UniqueIdItemJson $array */
        $metadata = PropertyItemMetadata::fromArray($array);

        $number = $array["unique_id"]["number"];
        $prefix = $array["unique_id"]["prefix"];

        return new self($metadata, $number, $prefix);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();
        $array["unique_id"] = [
            "number" => $this->number,
            "prefix" => $this->prefix,
        ];

        return $array;
    }

    public function metadata(): PropertyItemMetadata
    {
        return $this->metadata;
    }
}
