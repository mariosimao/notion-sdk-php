<?php

namespace Notion\Pages\Properties;

/**
 * @psalm-type EmailJson = array{
 *      id: string,
 *      type: "email",
 *      email: string,
 * }
 *
 * @psalm-immutable
 */
class Email implements PropertyInterface
{
    private const TYPE = Property::TYPE_EMAIL;

    private Property $property;

    private string $email;

    private function __construct(Property $property, string $email)
    {
        $this->property = $property;
        $this->email = $email;
    }

    public static function create(string $email): self
    {
        $property = Property::create("", self::TYPE);

        return new self($property, $email);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var EmailJson $array */

        $property = Property::fromArray($array);

        $email = $array[self::TYPE];

        return new self($property, $email);
    }

    public function toArray(): array
    {
        $array = $this->property->toArray();

        $array[self::TYPE] = $this->email;

        return $array;
    }

    public function property(): Property
    {
        return $this->property;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function withEmail(string $email): self
    {
        return new self($this->property, $email);
    }
}
