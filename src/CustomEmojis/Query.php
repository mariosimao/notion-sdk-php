<?php

namespace Notion\CustomEmojis;

use InvalidArgumentException;

/** @psalm-immutable */
final readonly class Query
{
    public const MAX_PAGE_SIZE = 100;

    private function __construct(
        public string|null $name = null,
        public string|null $startCursor = null,
        public int|null $pageSize = null,
    ) {
    }

    public static function all(): self
    {
        return new self();
    }

    /** Filter custom emojis by exact name match */
    public static function name(string $name): self
    {
        return new self($name);
    }

    public function changeName(string $name): self
    {
        return new self($name, $this->startCursor, $this->pageSize);
    }

    public function removeName(): self
    {
        return new self(null, $this->startCursor, $this->pageSize);
    }

    public function changeStartCursor(string $startCursor): self
    {
        return new self($this->name, $startCursor, $this->pageSize);
    }

    public function removeStartCursor(): self
    {
        return new self($this->name, null, $this->pageSize);
    }

    public function changePageSize(int $pageSize): self
    {
        if ($pageSize < 1 || $pageSize > self::MAX_PAGE_SIZE) {
            throw new InvalidArgumentException(
                "Page size must be between 1 and " . self::MAX_PAGE_SIZE . "."
            );
        }

        return new self($this->name, $this->startCursor, $pageSize);
    }

    /**
     * @internal
     *
     * @return array{ name?: string, start_cursor?: string, page_size?: int }
     */
    public function toArray(): array
    {
        $array = [];

        if ($this->name !== null) {
            $array["name"] = $this->name;
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
