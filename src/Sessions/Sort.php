<?php

namespace Notion\Sessions;

/**
 * Sort for session and session event queries.
 *
 * Sessions can be sorted by `createdAt()` and `updatedAt()`. Events can be
 * sorted by `createdAt()` and `sequence()`.
 *
 * @psalm-type SortJson = array{ property: string, direction: "ascending"|"descending" }
 *
 * @psalm-immutable
 */
final readonly class Sort
{
    private function __construct(
        public SortProperty $property,
        public SortDirection $direction,
    ) {
    }

    public static function createdAt(): self
    {
        return new self(SortProperty::CreatedAt, SortDirection::Descending);
    }

    public static function updatedAt(): self
    {
        return new self(SortProperty::UpdatedAt, SortDirection::Descending);
    }

    public static function sequence(): self
    {
        return new self(SortProperty::Sequence, SortDirection::Descending);
    }

    public function ascending(): self
    {
        return new self($this->property, SortDirection::Ascending);
    }

    public function descending(): self
    {
        return new self($this->property, SortDirection::Descending);
    }

    /**
     * @internal
     *
     * @return SortJson
     */
    public function toArray(): array
    {
        return [
            "property" => $this->property->value,
            "direction" => $this->direction->value,
        ];
    }
}
