<?php

namespace Notion\Views;

use Notion\Exceptions\ViewException;

/**
 * Parameters to list views of a database or data source
 *
 * @psalm-immutable
 */
final readonly class ListQuery
{
    public const MAX_PAGE_SIZE = 100;

    private function __construct(
        public string|null $databaseId,
        public string|null $dataSourceId,
        public string|null $startCursor,
        public int|null $pageSize,
    ) {
    }

    public static function database(string $databaseId): self
    {
        return new self($databaseId, null, null, null);
    }

    public static function dataSource(string $dataSourceId): self
    {
        return new self(null, $dataSourceId, null, null);
    }

    public function changeStartCursor(string $startCursor): self
    {
        return new self($this->databaseId, $this->dataSourceId, $startCursor, $this->pageSize);
    }

    public function changePageSize(int $pageSize): self
    {
        if ($pageSize < 1 || $pageSize > self::MAX_PAGE_SIZE) {
            throw ViewException::invalidPageSize(self::MAX_PAGE_SIZE);
        }

        return new self($this->databaseId, $this->dataSourceId, $this->startCursor, $pageSize);
    }

    /** @return array<string, string|int> */
    public function toArray(): array
    {
        $array = [];

        if ($this->databaseId !== null) {
            $array["database_id"] = $this->databaseId;
        }
        if ($this->dataSourceId !== null) {
            $array["data_source_id"] = $this->dataSourceId;
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
