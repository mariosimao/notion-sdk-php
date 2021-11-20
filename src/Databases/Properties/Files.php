<?php

namespace Notion\Databases\Properties;

/**
 * @psalm-type FilesJson = array{
 *      id: string,
 *      name: string,
 *      type: "file",
 *      file: array<empty, empty>,
 * }
 *
 * @psalm-immutable
 */
class Files implements PropertyInterface
{
    private const TYPE = Property::TYPE_FILES;

    private Property $property;

    private function __construct(Property $property)
    {
        $this->property = $property;
    }

    public static function create(string $propertyName = "Files"): self
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
        /** @psalm-var FilesJson $array */
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
