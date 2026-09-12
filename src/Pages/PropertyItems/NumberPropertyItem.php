<?php

namespace Notion\Pages\PropertyItems;

use Notion\Pages\Properties\PropertyType;

/**
 * @psalm-type NumberItemJson = array{
 *      id: string,
 *      type: "number",
 *      number: int|float|null,
 *      ...
 * }
 *
 * @psalm-immutable
 */
final readonly class NumberPropertyItem implements PropertyItemInterface
{
    private function __construct(
        private PropertyItemMetadata $metadata,
        public int|float|null $number,
    ) {
    }

    public static function create(int|float|null $number, string $id = ""): self
    {
        $metadata = PropertyItemMetadata::create($id, PropertyType::Number);

        return new self($metadata, $number);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var NumberItemJson $array */
        $metadata = PropertyItemMetadata::fromArray($array);
        $number = $array["number"] ?? null;

        return new self($metadata, $number);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();
        $array["number"] = $this->number;

        return $array;
    }

    public function metadata(): PropertyItemMetadata
    {
        return $this->metadata;
    }

    public function isEmpty(): bool
    {
        return $this->number === null;
    }
}
