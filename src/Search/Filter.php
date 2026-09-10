<?php

namespace Notion\Search;

/** @psalm-immutable */
final readonly class Filter
{
    private function __construct(
        public FilterValue $value,
        public FilterProperty $property,
    ) {
    }

    /** @psalm-mutation-free */
    public static function byPages(): self
    {
        return new self(FilterValue::Page, FilterProperty::Object);
    }

    /** @psalm-mutation-free */
    public static function byDataSources(): self
    {
        return new self(FilterValue::DataSource, FilterProperty::Object);
    }

    /**
     * @internal
     *
     * @return array{ value: "page"|"data_source", property: "object" }
     */
    public function toArray(): array
    {
        return [
            "value" => $this->value->value,
            "property" => $this->property->value,
        ];
    }
}
