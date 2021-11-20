<?php

namespace Notion\Pages\Properties;

use DateTimeImmutable;
use Notion\Common\Date;

/**
 * @psalm-type CreatedTimeJson = array{
 *      id: string,
 *      type: "created_time",
 *      created_time: string,
 * }
 *
 * @psalm-immutable
 */
class CreatedTime implements PropertyInterface
{
    private const TYPE = Property::TYPE_CREATED_TIME;

    private Property $property;

    private DateTimeImmutable $time;

    private function __construct(Property $property, DateTimeImmutable $time)
    {
        $this->property = $property;
        $this->time = $time;
    }

    public static function create(DateTimeImmutable $time): self
    {
        $property = Property::create("", self::TYPE);

        return new self($property, $time);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var CreatedTimeJson $array */

        $property = Property::fromArray($array);

        $time = new DateTimeImmutable($array[self::TYPE]);

        return new self($property, $time);
    }

    public function toArray(): array
    {
        $array = $this->property->toArray();

        $array[self::TYPE] = $this->time->format(Date::FORMAT);

        return $array;
    }

    public function property(): Property
    {
        return $this->property;
    }

    public function time(): DateTimeImmutable
    {
        return $this->time;
    }

    public function withTime(DateTimeImmutable $time): self
    {
        return new self($this->property, $time);
    }
}
