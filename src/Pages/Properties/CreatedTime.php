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
    private function __construct(
        private readonly PropertyMetadata $metadata,
        public readonly DateTimeImmutable $time,
    ) {
    }

    public static function create(DateTimeImmutable $time): self
    {
        $property = PropertyMetadata::create("", PropertyType::CreatedTime);

        return new self($property, $time);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var CreatedTimeJson $array */

        $property = PropertyMetadata::fromArray($array);

        $time = new DateTimeImmutable($array["created_time"]);

        return new self($property, $time);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();

        $array["created_time"] = $this->time->format(Date::FORMAT);

        return $array;
    }

    public function metadata(): PropertyMetadata
    {
        return $this->metadata;
    }

    public function changeTime(DateTimeImmutable $time): self
    {
        return new self($this->metadata, $time);
    }
}
