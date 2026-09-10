<?php

namespace Notion\Search;

/** @psalm-immutable */
final readonly class Query
{
    private function __construct(
        public string|null $query = null,
        public Filter|null $filter = null,
        public Sort|null $sort = null,
        public string|null $startCursor = null,
        public int|null $pageSize = null,
    ) {
    }

    public static function all(): self
    {
        return new self();
    }

    public static function title(string $query): self
    {
        return new self($query);
    }

    public function filterByPages(): self
    {
        return new self(
            $this->query,
            Filter::byPages(),
            $this->sort,
            $this->startCursor,
            $this->pageSize,
        );
    }

    public function filterByDataSources(): self
    {
        return new self(
            $this->query,
            Filter::byDataSources(),
            $this->sort,
            $this->startCursor,
            $this->pageSize,
        );
    }

    public function sortByLastEditedTime(SortDirection $direction): self
    {
        $sort = Sort::create()->byLastEditedTime();

        return new self(
            $this->query,
            $this->filter,
            $direction === SortDirection::Ascending ? $sort->ascending() : $sort->descending(),
            $this->startCursor,
            $this->pageSize,
        );
    }

    public function changeStartCursor(string $startCursor): self
    {
        return new self(
            $this->query,
            $this->filter,
            $this->sort,
            $startCursor,
            $this->pageSize,
        );
    }

    public function changePageSize(int $pageSize): self
    {
        return new self(
            $this->query,
            $this->filter,
            $this->sort,
            $this->startCursor,
            $pageSize,
        );
    }

    /**
     * @internal
     *
     * @return array{
     *      query?: string,
     *      filter?: array{ value: string, property: string },
     *      sort?: array{ direction: string, timestamp: string },
     *      start_cursor?: string,
     *      page_size?: int
     * }
     */
    public function toArray(): array
    {
        $array = [];

        if ($this->query !== null) {
            $array["query"] = $this->query;
        }

        if ($this->filter !== null) {
            $array["filter"] = $this->filter->toArray();
        }

        if ($this->sort !== null) {
            $array["sort"] = $this->sort->toArray();
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
