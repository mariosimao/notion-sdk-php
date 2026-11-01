<?php

namespace Notion\Search;

/**
 * @psalm-immutable
 *
 * @psalm-type FilterJson = array{
 *      value?: "page"|"data_source",
 *      property?: "object",
 *      in_trash?: bool
 * }
 */
final readonly class Filter
{
    private function __construct(
        public FilterValue|null $value,
        public FilterProperty|null $property,
        public bool|null $inTrash = null,
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
     * Filter by trash status only (`true` lists content in the trash)
     *
     * @psalm-mutation-free
     */
    public static function byTrash(bool $inTrash = true): self
    {
        return new self(null, null, $inTrash);
    }

    /** Keep the object filter and return only pages */
    public function pages(): self
    {
        return new self(FilterValue::Page, FilterProperty::Object, $this->inTrash);
    }

    /** Keep the trash filter and return only data sources */
    public function dataSources(): self
    {
        return new self(FilterValue::DataSource, FilterProperty::Object, $this->inTrash);
    }

    /** Keep the object filter and filter by trash status */
    public function changeInTrash(bool $inTrash): self
    {
        return new self($this->value, $this->property, $inTrash);
    }

    /**
     * @internal
     *
     * @return FilterJson
     */
    public function toArray(): array
    {
        $array = [];

        if ($this->value !== null && $this->property !== null) {
            $array["value"] = $this->value->value;
            $array["property"] = $this->property->value;
        }

        if ($this->inTrash !== null) {
            $array["in_trash"] = $this->inTrash;
        }

        return $array;
    }
}
