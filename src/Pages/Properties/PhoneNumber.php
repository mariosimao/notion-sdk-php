<?php

namespace Notion\Pages\Properties;

/**
 * @psalm-type PhoneNumberJson = array{
 *      id: string,
 *      type: "phone_number",
 *      phone_number: string,
 * }
 *
 * @psalm-immutable
 */
class PhoneNumber implements PropertyInterface
{
    private const TYPE = Property::TYPE_PHONE_NUMBER;

    private Property $property;

    private string $phone;

    private function __construct(Property $property, string $phone)
    {
        $this->property = $property;
        $this->phone = $phone;
    }

    public static function create(string $phone): self
    {
        $property = Property::create("", self::TYPE);

        return new self($property, $phone);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var PhoneNumberJson $array */

        $property = Property::fromArray($array);

        $phone = $array[self::TYPE];

        return new self($property, $phone);
    }

    public function toArray(): array
    {
        $array = $this->property->toArray();

        $array[self::TYPE] = $this->phone;

        return $array;
    }

    public function property(): Property
    {
        return $this->property;
    }

    public function phone(): string
    {
        return $this->phone;
    }

    public function withPhone(string $phone): self
    {
        return new self($this->property, $phone);
    }
}
