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
    private const TYPE = Property::TYPE_CREATED_BY;

    private Property $property;

    private function __construct(Property $property)
    {
        $this->property = $property;
    }

    public static function create(string $propertyName = "CreatedBy"): self
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
        /** @psalm-var CreatedByJson $array */
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
