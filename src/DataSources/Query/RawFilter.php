<?php

namespace Notion\DataSources\Query;

/**
 * Filter in its raw API representation.
 *
 * Used when reading filters returned by the API (e.g. on views), as they can
 * be arbitrarily nested.
 *
 * @psalm-immutable
 */
final readonly class RawFilter implements Filter
{
    private function __construct(
        private array $filter,
    ) {
    }

    public static function fromArray(array $filter): self
    {
        return new self($filter);
    }

    public function toArray(): array
    {
        return $this->filter;
    }
}
