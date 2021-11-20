<?php

namespace Notion\Pages\Properties;

use DateTimeImmutable;
use Notion\Common\Date as CommonDate;
use Notion\Common\RichText;

/**
 * @psalm-type DateJson = array{
 *      id: string,
 *      type: "date",
 *      date: array{
 *          start: string,
 *          end?: string,
 *      },
 * }
 *
 * @psalm-immutable
 */
class Date implements PropertyInterface
{
    private const TYPE = Property::TYPE_DATE;

    private Property $property;

    private DateTimeImmutable $start;
    private DateTimeImmutable|null $end;

    private function __construct(
        Property $property,
        DateTimeImmutable $start,
        DateTimeImmutable|null $end,
    ) {
        $this->property = $property;
        $this->start = $start;
        $this->end = $end;
    }

    public static function create(
        DateTimeImmutable $start,
        DateTimeImmutable|null $end = null,
    ): self {
        $property = Property::create("", self::TYPE);

        return new self($property, $start, $end);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var DateJson $array */

        $property = Property::fromArray($array);

        $start = new DateTimeImmutable($array[self::TYPE]["start"]);
        $end = isset($array[self::TYPE]["end"]) ?
            new DateTimeImmutable($array[self::TYPE]["end"]) : null;

        return new self($property, $start, $end);
    }

    public function toArray(): array
    {
        $array = $this->property->toArray();

        $array[self::TYPE] = [
            "start" => $this->start->format(CommonDate::FORMAT),
            "end"   => $this->end?->format(CommonDate::FORMAT),
        ];

        return $array;
    }

    public function property(): Property
    {
        return $this->property;
    }

    public function start(): DateTimeImmutable
    {
        return $this->start;
    }

    public function end(): DateTimeImmutable|null
    {
        return $this->end;
    }

    public function withStart(DateTimeImmutable $start): self
    {
        return new self($this->property, $start, $this->end);
    }

    public function withEnd(DateTimeImmutable $end): self
    {
        return new self($this->property, $this->start, $end);
    }

    public function withoutEnd(): self
    {
        return new self($this->property, $this->start, null);
    }

    public function isRange(): bool
    {
        return $this->end !== null;
    }
}
