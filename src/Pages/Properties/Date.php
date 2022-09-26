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
    private function __construct(
        private readonly PropertyMetadata $metadata,
        public readonly DateTimeImmutable $start,
        public readonly DateTimeImmutable|null $end,
    ) {
    }

    public static function create(
        DateTimeImmutable $start,
        DateTimeImmutable|null $end = null,
    ): self {
        $property = PropertyMetadata::create("", PropertyType::Date);

        return new self($property, $start, $end);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var DateJson $array */

        $property = PropertyMetadata::fromArray($array);

        $start = new DateTimeImmutable($array["date"]["start"]);
        $end = isset($array["date"]["end"]) ?
            new DateTimeImmutable($array["date"]["end"]) : null;

        return new self($property, $start, $end);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();

        $array["date"] = [
            "start" => $this->start->format(CommonDate::FORMAT),
            "end"   => $this->end?->format(CommonDate::FORMAT),
        ];

        return $array;
    }

    public function metadata(): PropertyMetadata
    {
        return $this->metadata;
    }

    public function changeStart(DateTimeImmutable $start): self
    {
        return new self($this->metadata, $start, $this->end);
    }

    public function changeEnd(DateTimeImmutable $end): self
    {
        return new self($this->metadata, $this->start, $end);
    }

    public function removeEnd(): self
    {
        return new self($this->metadata, $this->start, null);
    }

    public function isRange(): bool
    {
        return $this->end !== null;
    }
}
