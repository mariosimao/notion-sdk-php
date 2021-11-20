<?php

namespace Notion\Pages\Properties;

use Notion\Common\RichText;

/**
 * @psalm-type NumberJson = array{
 *      id: string,
 *      type: "number",
 *      number: int|float,
 * }
 *
 * @psalm-immutable
 */
class Number implements PropertyInterface
{
    private const TYPE = Property::TYPE_NUMBER;

    private Property $property;

    private int|float $number;

    private function __construct(Property $property, int|float $number)
    {
        $this->property = $property;
        $this->number = $number;
    }

    public static function create(int|float $number): self
    {
        $property = Property::create("", self::TYPE);

        return new self($property, $number);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var NumberJson $array */

        $property = Property::fromArray($array);

        $number = $array[self::TYPE];

        return new self($property, $number);
    }

    public function toArray(): array
    {
        $array = $this->property->toArray();

        $array[self::TYPE] = $this->number;

        return $array;
    }

    public function property(): Property
    {
        return $this->property;
    }

    public function number(): int|float
    {
        return $this->number;
    }

    public function withNumber(int|float $number): self
    {
        return new self($this->property, $number);
    }
}
