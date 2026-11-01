<?php

namespace Notion\DataSources;

use Exception;
use Notion\DataSources\Query\Filter;
use Notion\DataSources\Query\Sort;

/** @psalm-immutable */
final readonly class Query
{
    public const MAX_PAGE_SIZE = 100;

    /** @param Sort[] $sorts */
    private function __construct(
        public Filter|null $filter,
        public array $sorts,
        public string|null $startCursor,
        public int $pageSize,
        public bool $isArchived = false,
    ) {
    }

    public static function create(): self
    {
        return new self(null, [], null, self::MAX_PAGE_SIZE);
    }

    public function changeFilter(Filter $filter): self
    {
        return new self($filter, $this->sorts, $this->startCursor, $this->pageSize, $this->isArchived);
    }

    /** Add new sort with lowest priority */
    public function addSort(Sort $sort): self
    {
        $sorts = $this->sorts;
        $sorts[] = $sort;

        return new self($this->filter, $sorts, $this->startCursor, $this->pageSize, $this->isArchived);
    }

    /** Replace all sorts */
    public function changeSorts(Sort ...$sorts): self
    {
        return new self($this->filter, $sorts, $this->startCursor, $this->pageSize, $this->isArchived);
    }

    public function changeStartCursor(string $startCursor): self
    {
        return new self($this->filter, $this->sorts, $startCursor, $this->pageSize, $this->isArchived);
    }

    public function changePageSize(int $pageSize): self
    {
        if ($pageSize < 0 || $pageSize > self::MAX_PAGE_SIZE) {
            throw new Exception("Maximum page size: " . self::MAX_PAGE_SIZE);
        }

        return new self($this->filter, $this->sorts, $this->startCursor, $pageSize, $this->isArchived);
    }

    /**
     * Query archived pages instead of non-archived pages.
     *
     * The archive selector is applied before filters and sorts.
     */
    public function changeIsArchived(bool $isArchived = true): self
    {
        return new self($this->filter, $this->sorts, $this->startCursor, $this->pageSize, $isArchived);
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

        if ($this->isArchived) {
            $array["is_archived"] = true;
        }

        return $array;
    }
}
