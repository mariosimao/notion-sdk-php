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
    private const TYPE = Property::TYPE_PEOPLE;

    private Property $property;

    private function __construct(Property $property)
    {
        $this->property = $property;
    }

    public static function create(string $propertyName = "People"): self
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
        /** @psalm-var PeopleJson $array */
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
