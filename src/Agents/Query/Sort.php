<?php

namespace Notion\Agents\Query;

use Notion\Search\SortDirection;

/** @psalm-immutable */
final readonly class Sort
{
    private function __construct(
        public SortProperty $property,
        public SortDirection $direction,
    ) {
    }

    public static function createdTime(): self
    {
        return new self(SortProperty::CreatedTime, SortDirection::Descending);
    }

    public static function lastRunAt(): self
    {
        return new self(SortProperty::LastRunAt, SortDirection::Descending);
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
     * @return array{ property: string, direction: string }
     */
    public function toArray(): array
    {
        return [
            "property" => $this->property->value,
            "direction" => $this->direction->value,
        ];
    }
}
