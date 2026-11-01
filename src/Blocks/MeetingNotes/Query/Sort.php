<?php

namespace Notion\Blocks\MeetingNotes\Query;

/** @psalm-immutable */
final readonly class Sort
{
    private const ORDER_ASCENDING = "ascending";
    private const ORDER_DESCENDING = "descending";

    /** @psalm-param self::ORDER_* $direction */
    private function __construct(
        public Property $property,
        private string $direction,
    ) {
    }

    public static function property(Property $property): self
    {
        return new self($property, self::ORDER_ASCENDING);
    }

    public static function title(): self
    {
        return self::property(Property::Title);
    }

    public static function attendees(): self
    {
        return self::property(Property::Attendees);
    }

    public static function createdTime(): self
    {
        return self::property(Property::CreatedTime);
    }

    public static function createdBy(): self
    {
        return self::property(Property::CreatedBy);
    }

    public static function lastEditedTime(): self
    {
        return self::property(Property::LastEditedTime);
    }

    public static function lastEditedBy(): self
    {
        return self::property(Property::LastEditedBy);
    }

    public function ascending(): self
    {
        return new self($this->property, self::ORDER_ASCENDING);
    }

    public function descending(): self
    {
        return new self($this->property, self::ORDER_DESCENDING);
    }

    public function toArray(): array
    {
        return [
            "property"  => $this->property->value,
            "direction" => $this->direction,
        ];
    }
}
