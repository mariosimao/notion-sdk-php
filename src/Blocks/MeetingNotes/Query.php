<?php

namespace Notion\Blocks\MeetingNotes;

use InvalidArgumentException;
use Notion\Blocks\MeetingNotes\Query\CompoundFilter;
use Notion\Blocks\MeetingNotes\Query\Filter;
use Notion\Blocks\MeetingNotes\Query\Sort;

/**
 * Meeting notes query
 *
 * The endpoint does not use cursor-based pagination: use the filter, sorts
 * and limit to refine the result set.
 *
 * @psalm-immutable
 */
final readonly class Query
{
    public const MAX_LIMIT = 50;
    public const MAX_SORTS = 100;

    /**
     * @param Sort[] $sorts
     * @param int|null $limit `null` uses the server default (50)
     */
    private function __construct(
        public Filter|null $filter,
        public array $sorts,
        public int|null $limit,
    ) {
    }

    public static function create(): self
    {
        return new self(null, [], null);
    }

    public function changeFilter(Filter $filter): self
    {
        return new self($filter, $this->sorts, $this->limit);
    }

    public function removeFilter(): self
    {
        return new self(null, $this->sorts, $this->limit);
    }

    /** Add new sort with lowest priority */
    public function addSort(Sort $sort): self
    {
        $sorts = $this->sorts;
        $sorts[] = $sort;

        return $this->changeSorts(...$sorts);
    }

    /** Replace all sorts, earlier sorts take precedence */
    public function changeSorts(Sort ...$sorts): self
    {
        if (count($sorts) > self::MAX_SORTS) {
            throw new InvalidArgumentException("Maximum number of sorts: " . self::MAX_SORTS);
        }

        return new self($this->filter, $sorts, $this->limit);
    }

    public function changeLimit(int $limit): self
    {
        if ($limit < 1 || $limit > self::MAX_LIMIT) {
            throw new InvalidArgumentException("Limit must be between 1 and " . self::MAX_LIMIT . ".");
        }

        return new self($this->filter, $this->sorts, $limit);
    }

    public function removeLimit(): self
    {
        return new self($this->filter, $this->sorts, null);
    }

    public function toArray(): array
    {
        $array = [];

        if ($this->filter !== null) {
            // A single property filter is wrapped in an "and" combinator,
            // which is the root shape required by the API schema
            $filter = $this->filter instanceof CompoundFilter ?
                $this->filter : CompoundFilter::and($this->filter);

            $array["filter"] = $filter->toArray();
        }

        if (count($this->sorts) > 0) {
            $array["sort"] = array_map(fn (Sort $s) => $s->toArray(), $this->sorts);
        }

        if ($this->limit !== null) {
            $array["limit"] = $this->limit;
        }

        return $array;
    }
}
