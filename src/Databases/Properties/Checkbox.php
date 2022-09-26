<?php

namespace Notion\Databases\Properties;

/**
 * @psalm-type CheckboxJson = array{
 *      id: string,
 *      name: string,
 *      type: "checkbox",
 *      checkbox: array<empty, empty>,
 * }
 *
 * @psalm-immutable
 */
class Checkbox implements PropertyInterface
{
    private function __construct(
        private readonly PropertyMetadata $metadata,
    ) {
    }

    public static function create(string $propertyName = "Checkbox"): self
    {
        $metadata = PropertyMetadata::create("", $propertyName, PropertyType::Checkbox);

        return new self($metadata);
    }

    public function metadata(): PropertyMetadata
    {
        return $this->metadata;
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var CheckboxJson $array */
        $property = PropertyMetadata::fromArray($array);

        return new self($property);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();
        $array["checkbox"] = new \stdClass();

        return $array;
    }
}
