<?php

namespace Notion\Databases\Properties;

/**
 * @psalm-type DateJson = array{
 *      id: string,
 *      name: string,
 *      type: "date",
 *      date: array<empty, empty>,
 * }
 *
 * @psalm-immutable
 */
class Date implements PropertyInterface
{
    private function __construct(
        private readonly PropertyMetadata $metadata,
    ) {
    }

    public static function create(string $propertyName = "Date"): self
    {
        $property = PropertyMetadata::create("", $propertyName, PropertyType::Date);

        return new self($property);
    }

    public function metadata(): PropertyMetadata
    {
        return $this->metadata;
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var DateJson $array */
        $property = PropertyMetadata::fromArray($array);

        return new self($property);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();
        $array["date"] = new \stdClass();

        return $array;
    }
}
