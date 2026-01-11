<?php

namespace Notion\Search;

/** @psalm-immutable */
class Filter
{
    private function __construct(
        public readonly FilterValue $value,
        public readonly FilterProperty $property,
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
