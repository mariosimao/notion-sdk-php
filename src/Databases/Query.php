<?php

namespace Notion\Databases;

use Exception;
use Notion\Databases\Query\Filter;
use Notion\Databases\Query\Sort;

/** @psalm-immutable */
class Query
{
    public const MAX_PAGE_SIZE = 100;

    private Filter|null $filter;

    /** @var list<Sort> */
    private array $sorts;

    private string|null $startCursor;

    private int $pageSize;

    /** @param list<Sort> $sorts */
    private function __construct(
        Filter|null $filter,
        array $sorts,
        string|null $startCursor,
        int $pageSize
    ) {
        $this->filter = $filter;
        $this->sorts = $sorts;
        $this->startCursor = $startCursor;
        $this->pageSize = $pageSize;
    }

    public static function create(): self
    {
        return new self(null, [], null, self::MAX_PAGE_SIZE);
    }

    public function withFilter(Filter $filter): self
    {
        return new self($filter, $this->sorts, $this->startCursor, $this->pageSize);
    }

    /** Add new sort with lowest priority */
    public function withAddedSort(Sort $sort): self
    {
        $sorts = $this->sorts;
        $sorts[] = $sort;

        return new self($this->filter, $sorts, $this->startCursor, $this->pageSize);
    }

    /**
     * Replace all sorts
     *
     * @param list<Sort> $sorts
     */
    public function withSorts(array $sorts): self
    {
        return new self($this->filter, $sorts, $this->startCursor, $this->pageSize);
    }

    public function withStartCursor(string $startCursor): self
    {
        return new self($this->filter, $this->sorts, $startCursor, $this->pageSize);
    }

    public function withPageSize(int $pageSize): self
    {
        if ($pageSize < 0 || $pageSize > self::MAX_PAGE_SIZE) {
            throw new Exception("Maximum page size: " . self::MAX_PAGE_SIZE);
        }

        return new self($this->filter, $this->sorts, $this->startCursor, $pageSize);
    }

    public function filter(): Filter|null
    {
        return $this->filter;
    }

    /** @return list<Sort> */
    public function sorts(): array
    {
        return $this->sorts;
    }

    public function startCursor(): string|null
    {
        return $this->startCursor;
    }

    public function pageSize(): int
    {
        return $this->pageSize;
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
