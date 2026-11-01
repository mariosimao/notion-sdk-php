<?php

namespace Notion\Views;

use Notion\DataSources\Query\Condition;
use Notion\DataSources\Query\Filter;

/**
 * Filter pinned to the filter bar of a view
 *
 * @psalm-immutable
 */
final readonly class QuickFilter
{
    private function __construct(
        public string $property,
        public array $condition,
    ) {
    }

    /**
     * Create a quick filter from a property filter
     *
     * Example: `QuickFilter::fromFilter(SelectFilter::property("Status")->equals("Done"))`
     */
    public static function fromFilter(Filter&Condition $filter): self
    {
        $condition = $filter->toArray();
        unset($condition[$filter->propertyType()]);

        return new self($filter->propertyName(), $condition);
    }

    /**
     * Pin a property to the filter bar without any filter criteria
     *
     * @param string $property Property name or ID
     */
    public static function withoutCriteria(string $property): self
    {
        return new self($property, []);
    }

    /** @internal */
    public static function fromArray(string $property, array $condition): self
    {
        return new self($property, $condition);
    }

    /** Condition in the API format (property filter without the "property" key) */
    public function toArray(): array|object
    {
        return $this->condition === [] ? new \stdClass() : $this->condition;
    }
}
