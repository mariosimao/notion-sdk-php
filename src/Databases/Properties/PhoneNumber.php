<?php

namespace Notion\Databases\Properties;

/**
 * @psalm-type PhoneNumberJson = array{
 *      id: string,
 *      name: string,
 *      type: "phone_number",
 *      phone_number: array<empty, empty>,
 * }
 *
 * @psalm-immutable
 */
class PhoneNumber implements PropertyInterface
{
    private const TYPE = Property::TYPE_PHONE_NUMBER;

    private Property $property;

    private function __construct(Property $property)
    {
        $this->property = $property;
    }

    public static function create(string $propertyName = "PhoneNumber"): self
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
        /** @psalm-var PhoneNumberJson $array */
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
