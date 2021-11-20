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
    private const TYPE = Property::TYPE_CHECKBOX;

    private Property $property;

    private function __construct(Property $property)
    {
        $this->property = $property;
    }

    public static function create(string $propertyName = "Checkbox"): self
    {
        $property = Property::create("", $propertyName, self::TYPE);

        return new self($property);
    }

    public function property(): Property
    {
        return $this->property;
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var CheckboxJson $array */
        $property = Property::fromArray($array);

        return new self($property);
    }

    public function toArray(): array
    {
        $array = $this->property->toArray();
        $array[self::TYPE] = [];

        return $array;
    }
}
