<?php

namespace Notion\Databases\Properties;

/**
 * @psalm-type TitleJson = array{
 *      id: "title",
 *      name: string,
 *      type: "title",
 *      title: array<empty, empty>,
 * }
 *
 * @psalm-immutable
 */
class Title implements PropertyInterface
{
    private const TYPE = Property::TYPE_TITLE;

    private Property $property;

    private function __construct(Property $property)
    {
        $this->property = $property;
    }

    public static function create(string $propertyName = "Title"): self
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
        /** @psalm-var TitleJson $array */
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
