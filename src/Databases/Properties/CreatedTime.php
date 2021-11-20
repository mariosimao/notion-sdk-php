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
    private const TYPE = Property::TYPE_CREATED_TIME;

    private Property $property;

    private function __construct(Property $property)
    {
        $this->property = $property;
    }

    public static function create(string $propertyName = "CreatedTime"): self
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
        /** @psalm-var CreatedTimeJson $array */
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
