<?php

namespace Notion\Search;

/** @psalm-immutable */
class Sort
{
    private function __construct(
        public readonly SortDirection $direction,
        public readonly SortTimestamp $timestamp,
    ) {
    }

    /** @psalm-mutation-free */
    public static function create(): self
    {
        return new self(SortDirection::Descending, SortTimestamp::LastEditedTime);
    }

    public function byLastEditedTime(): self
    {
        return new self($this->direction, SortTimestamp::LastEditedTime);
    }

    public function ascending(): self
    {
        return new self(SortDirection::Ascending, $this->timestamp);
    }

    public function descending(): self
    {
        return new self(SortDirection::Descending, $this->timestamp);
    }

    /**
     * @internal
     *
     * @return array{ direction: "ascending"|"descending", timestamp: "last_edited_time" }
     */
    public function toArray(): array
    {
        return [
            "direction" => $this->direction->value,
            "timestamp" => $this->timestamp->value,
        ];
    }
}
