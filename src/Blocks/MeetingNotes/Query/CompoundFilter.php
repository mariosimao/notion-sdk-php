<?php

namespace Notion\Blocks\MeetingNotes\Query;

use InvalidArgumentException;

/**
 * Combine meeting note filters with "and" / "or"
 *
 * Only one level of nesting is supported: a compound filter nested inside
 * another one can only contain property filters.
 *
 * @psalm-immutable
 */
final readonly class CompoundFilter implements Filter
{
    public const MAX_FILTERS = 100;

    private const TYPE_AND = "and";
    private const TYPE_OR  = "or";

    /** @var Filter[] */
    private array $filters;

    /** @param self::TYPE_* $type */
    private function __construct(
        private string $type,
        Filter ...$filters,
    ) {
        if (count($filters) > self::MAX_FILTERS) {
            throw new InvalidArgumentException("Maximum number of filters: " . self::MAX_FILTERS);
        }

        foreach ($filters as $filter) {
            if ($filter instanceof self && !$filter->hasOnlyPropertyFilters()) {
                throw new InvalidArgumentException(
                    "Nested compound filters can only contain property filters.",
                );
            }
        }

        $this->filters = $filters;
    }

    /** @psalm-pure */
    public static function and(Filter ...$filters): self
    {
        return new self(self::TYPE_AND, ...$filters);
    }

    /** @psalm-pure */
    public static function or(Filter ...$filters): self
    {
        return new self(self::TYPE_OR, ...$filters);
    }

    /** @return Filter[] */
    public function filters(): array
    {
        return $this->filters;
    }

    public function toArray(): array
    {
        return [
            "operator" => $this->type,
            "filters"  => array_map(fn (Filter $f) => $f->toArray(), $this->filters),
        ];
    }

    private function hasOnlyPropertyFilters(): bool
    {
        foreach ($this->filters as $filter) {
            if (!$filter instanceof PropertyFilter) {
                return false;
            }
        }

        return true;
    }
}
