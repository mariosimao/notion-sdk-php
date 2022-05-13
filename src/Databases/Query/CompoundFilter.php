<?php

namespace Notion\Databases\Query;

/** @psalm-immutable */
class CompoundFilter implements Filter
{
    private const TYPE_AND = "and";
    private const TYPE_OR  = "or";

    /** @var self::TYPE_* */
    private string $type;
    /** @var Filter[] */
    private array $filters;

    /** @param self::TYPE_* $type */
    private function __construct(string $type, Filter ...$filters)
    {
        $this->type = $type;
        $this->filters = $filters;
    }

    public static function and(Filter ...$filters): self
    {
        return new self(self::TYPE_AND, ...$filters);
    }

    public static function or(Filter ...$filters): self
    {
        return new self(self::TYPE_OR, ...$filters);
    }

    public function toArray(): array
    {
        return [
            $this->type => array_map(fn (Filter $f) => $f->toArray(), $this->filters)
        ];
    }
}
