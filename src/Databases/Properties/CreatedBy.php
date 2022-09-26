<?php

namespace Notion\Databases\Properties;

/**
 * @psalm-type CreatedByJson = array{
 *      id: string,
 *      name: string,
 *      type: "created_by",
 *      created_by: array<empty, empty>,
 * }
 *
 * @psalm-immutable
 */
class CreatedBy implements PropertyInterface
{
    private function __construct(
        private readonly PropertyMetadata $metadata,
    ) {
    }

    public static function create(string $propertyName = "CreatedBy"): self
    {
        $property = PropertyMetadata::create("", $propertyName, PropertyType::CreatedBy);

        return new self($property);
    }

    public function metadata(): PropertyMetadata
    {
        return $this->metadata;
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var CreatedByJson $array */
        $property = PropertyMetadata::fromArray($array);

        return new self($property);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();
        $array["created_by"] = new \stdClass();

        return $array;
    }
}
