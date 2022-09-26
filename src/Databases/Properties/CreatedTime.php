<?php

namespace Notion\Databases\Properties;

/**
 * @psalm-type CreatedTimeJson = array{
 *      id: string,
 *      name: string,
 *      type: "created_time",
 *      created_time: array<empty, empty>,
 * }
 *
 * @psalm-immutable
 */
class CreatedTime implements PropertyInterface
{
    private function __construct(
        private readonly PropertyMetadata $metadata,
    ) {
    }

    public static function create(string $propertyName = "CreatedTime"): self
    {
        $property = PropertyMetadata::create("", $propertyName, PropertyType::CreatedTime);

        return new self($property);
    }

    public function metadata(): PropertyMetadata
    {
        return $this->metadata;
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var CreatedTimeJson $array */
        $property = PropertyMetadata::fromArray($array);

        return new self($property);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();
        $array["created_time"] = new \stdClass();

        return $array;
    }
}
