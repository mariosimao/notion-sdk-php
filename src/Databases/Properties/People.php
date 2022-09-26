<?php

namespace Notion\Databases\Properties;

/**
 * @psalm-type PeopleJson = array{
 *      id: string,
 *      name: string,
 *      type: "people",
 *      people: array<empty, empty>,
 * }
 *
 * @psalm-immutable
 */
class People implements PropertyInterface
{
    private function __construct(
        private readonly PropertyMetadata $metadata,
    ) {
    }

    public static function create(string $propertyName = "People"): self
    {
        $metadata = PropertyMetadata::create("", $propertyName, PropertyType::People);

        return new self($metadata);
    }

    public function metadata(): PropertyMetadata
    {
        return $this->metadata;
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var PeopleJson $array */
        $metadata = PropertyMetadata::fromArray($array);

        return new self($metadata);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();
        $array["people"] = new \stdClass();

        return $array;
    }
}
