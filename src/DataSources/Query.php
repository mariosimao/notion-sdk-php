<?php

namespace Notion\DataSources;

use Exception;
use Notion\DataSources\Query\Filter;
use Notion\DataSources\Query\Sort;

/** @psalm-immutable */
final readonly class Query
{
    public const MAX_PAGE_SIZE = 100;

    /**
     * @param Sort[] $sorts
     * @param list<string> $filterProperties Sent as query parameters, not in the request body
     */
    private function __construct(
        public Filter|null $filter,
        public array $sorts,
        public string|null $startCursor,
        public int $pageSize,
        public array $filterProperties,
    ) {
    }

    public static function create(): self
    {
        return new self(null, [], null, self::MAX_PAGE_SIZE, []);
    }

    public function changeFilter(Filter $filter): self
    {
        return new self($filter, $this->sorts, $this->startCursor, $this->pageSize, $this->filterProperties);
    }

    /** Only return the given property IDs on each page */
    public function changeFilterProperties(string ...$propertyIds): self
    {
        return new self($this->filter, $this->sorts, $this->startCursor, $this->pageSize, array_values($propertyIds));
    }

    /** Add new sort with lowest priority */
    public function addSort(Sort $sort): self
    {
        $sorts = $this->sorts;
        $sorts[] = $sort;

        return new self($this->filter, $sorts, $this->startCursor, $this->pageSize, $this->filterProperties);
    }

    /** Replace all sorts */
    public function changeSorts(Sort ...$sorts): self
    {
        return new self($this->filter, $sorts, $this->startCursor, $this->pageSize, $this->filterProperties);
    }

    public function changeStartCursor(string $startCursor): self
    {
        return new self($this->filter, $this->sorts, $startCursor, $this->pageSize, $this->filterProperties);
    }

    public function changePageSize(int $pageSize): self
    {
        if ($pageSize < 0 || $pageSize > self::MAX_PAGE_SIZE) {
            throw new Exception("Maximum page size: " . self::MAX_PAGE_SIZE);
        }

        return new self($this->filter, $this->sorts, $this->startCursor, $pageSize, $this->filterProperties);
    }

    public function toArray(): array
    {
        $array = [
            "sorts"     => array_map(fn (Sort $s) => $s->toArray(), $this->sorts),
            "page_size" => $this->pageSize,
        ];

        if ($this->filter !== null) {
            $array["filter"] = $this->filter->toArray();
        }

        if ($this->startCursor !== null) {
            $array["start_cursor"] = $this->startCursor;
        }

        return $array;
    }
}
