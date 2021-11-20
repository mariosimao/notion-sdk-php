<?php

namespace Notion\Databases\Properties;

/**
 * @psalm-type LastEditedByJson = array{
 *      id: string,
 *      name: string,
 *      type: "last_edited_by",
 *      last_edited_by: array<empty, empty>,
 * }
 *
 * @psalm-immutable
 */
class LastEditedBy implements PropertyInterface
{
    private const TYPE = Property::TYPE_LAST_EDITED_BY;

    private Property $property;

    private function __construct(Property $property)
    {
        $this->property = $property;
    }

    public static function create(string $propertyName = "LastEditedBy"): self
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
        /** @psalm-var LastEditedByJson $array */
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
