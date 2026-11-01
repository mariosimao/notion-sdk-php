<?php

namespace Notion\Sessions\Events;

use Notion\Sessions\Sort;

/**
 * @psalm-import-type SortJson from Sort
 *
 * @psalm-immutable
 */
final readonly class EventQuery
{
    public const MAX_PAGE_SIZE = 100;

    /** @param list<Sort> $sorts */
    private function __construct(
        public EventFilter|null $filter,
        public array $sorts,
        public string|null $startCursor,
        public int|null $pageSize,
    ) {
    }

    public static function create(): self
    {
        return new self(null, [], null, null);
    }

    public function changeFilter(EventFilter $filter): self
    {
        return new self($filter, $this->sorts, $this->startCursor, $this->pageSize);
    }

    /** Up to two sorts, by `Sort::sequence()` or `Sort::createdAt()`. Defaults to sequence ascending. */
    public function changeSorts(Sort ...$sorts): self
    {
        return new self($this->filter, array_values($sorts), $this->startCursor, $this->pageSize);
    }

    public function changeStartCursor(string|null $startCursor): self
    {
        return new self($this->filter, $this->sorts, $startCursor, $this->pageSize);
    }

    public function changePageSize(int $pageSize): self
    {
        return new self($this->filter, $this->sorts, $this->startCursor, $pageSize);
    }

    /**
     * @internal
     *
     * @return array{
     *      filter?: array,
     *      sorts?: list<SortJson>,
     *      start_cursor?: string,
     *      page_size?: int,
     * }
     */
    public function toArray(): array
    {
        $array = [];

        if ($this->filter !== null) {
            $array["filter"] = $this->filter->toArray();
        }
        if ($this->sorts !== []) {
            $array["sorts"] = array_map(fn (Sort $s) => $s->toArray(), $this->sorts);
        }
        if ($this->startCursor !== null) {
            $array["start_cursor"] = $this->startCursor;
        }
        if ($this->pageSize !== null) {
            $array["page_size"] = $this->pageSize;
        }

        return $array;
    }
}
