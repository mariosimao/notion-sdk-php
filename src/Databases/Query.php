<?php

namespace Notion\Databases;

use Exception;
use Notion\Databases\Query\Filter;
use Notion\Databases\Query\Sort;

/** @psalm-immutable */
class Query
{
    public const MAX_PAGE_SIZE = 100;

    /** @param Sort[] $sorts */
    private function __construct(
        public readonly Filter|null $filter,
        public readonly array $sorts,
        public readonly string|null $startCursor,
        public readonly int $pageSize,
    ) {
    }

    public static function create(): self
    {
        return new self(null, [], null, self::MAX_PAGE_SIZE);
    }

    public function changeFilter(Filter $filter): self
    {
        return new self($filter, $this->sorts, $this->startCursor, $this->pageSize);
    }

    /** Add new sort with lowest priority */
    public function addSort(Sort $sort): self
    {
        $sorts = $this->sorts;
        $sorts[] = $sort;

        return new self($this->filter, $sorts, $this->startCursor, $this->pageSize);
    }

    /**
     * @deprecated 1.1.0 This method will be removed in future versions. Use 'addSort' instead.
     * @see \Notion\Databases\Query::addSort()
     */
    public function changeAddedSort(Sort $sort): self
    {
        return $this->addSort($sort);
    }

    /** Replace all sorts */
    public function changeSorts(Sort ...$sorts): self
    {
        return new self($this->filter, $sorts, $this->startCursor, $this->pageSize);
    }

    public function changeStartCursor(string $startCursor): self
    {
        return new self($this->filter, $this->sorts, $startCursor, $this->pageSize);
    }

    public function changePageSize(int $pageSize): self
    {
        if ($pageSize < 0 || $pageSize > self::MAX_PAGE_SIZE) {
            throw new Exception("Maximum page size: " . self::MAX_PAGE_SIZE);
        }

        return new self($this->filter, $this->sorts, $this->startCursor, $pageSize);
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
