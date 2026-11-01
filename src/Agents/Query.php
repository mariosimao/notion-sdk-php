<?php

namespace Notion\Agents;

use Notion\Agents\Query\Filter;
use Notion\Agents\Query\Sort;

/** @psalm-immutable */
final readonly class Query
{
    public const MAX_PAGE_SIZE = 100;

    /** @param list<Sort> $sorts */
    private function __construct(
        public string|null $query = null,
        public Filter|null $filter = null,
        public array $sorts = [],
        public string|null $startCursor = null,
        public int|null $pageSize = null,
        public bool $verbose = false,
        public bool $includeDeleted = false,
    ) {
    }

    public static function create(): self
    {
        return new self();
    }

    /** Search agent names and descriptions (case-insensitive substring match) */
    public function changeQuery(string $query): self
    {
        return new self(
            $query,
            $this->filter,
            $this->sorts,
            $this->startCursor,
            $this->pageSize,
            $this->verbose,
            $this->includeDeleted,
        );
    }

    public function changeFilter(Filter $filter): self
    {
        return new self(
            $this->query,
            $filter,
            $this->sorts,
            $this->startCursor,
            $this->pageSize,
            $this->verbose,
            $this->includeDeleted,
        );
    }

    public function changeSorts(Sort ...$sorts): self
    {
        return new self(
            $this->query,
            $this->filter,
            array_values($sorts),
            $this->startCursor,
            $this->pageSize,
            $this->verbose,
            $this->includeDeleted,
        );
    }

    public function changeStartCursor(string|null $startCursor): self
    {
        return new self(
            $this->query,
            $this->filter,
            $this->sorts,
            $startCursor,
            $this->pageSize,
            $this->verbose,
            $this->includeDeleted,
        );
    }

    public function changePageSize(int $pageSize): self
    {
        return new self(
            $this->query,
            $this->filter,
            $this->sorts,
            $this->startCursor,
            $pageSize,
            $this->verbose,
            $this->includeDeleted,
        );
    }

    /** Include the inline instructions of each agent */
    public function withInstructions(): self
    {
        return new self(
            $this->query,
            $this->filter,
            $this->sorts,
            $this->startCursor,
            $this->pageSize,
            true,
            $this->includeDeleted,
        );
    }

    /** Include soft-deleted agents */
    public function includeDeleted(): self
    {
        return new self(
            $this->query,
            $this->filter,
            $this->sorts,
            $this->startCursor,
            $this->pageSize,
            $this->verbose,
            true,
        );
    }

    /**
     * @internal
     *
     * @return array{
     *      query?: string,
     *      filter?: array<string, mixed>,
     *      sorts?: list<array{ property: string, direction: string }>,
     *      start_cursor?: string,
     *      page_size?: int,
     *      verbose?: true,
     *      include_deleted?: true,
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

        if ($this->sorts !== []) {
            $array["sorts"] = array_map(fn(Sort $s): array => $s->toArray(), $this->sorts);
        }

        if ($this->startCursor !== null) {
            $array["start_cursor"] = $this->startCursor;
        }

        if ($this->pageSize !== null) {
            $array["page_size"] = $this->pageSize;
        }

        if ($this->verbose) {
            $array["verbose"] = true;
        }

        if ($this->includeDeleted) {
            $array["include_deleted"] = true;
        }

        return $array;
    }
}
