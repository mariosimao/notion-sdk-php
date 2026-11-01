<?php

namespace Notion\Sessions\Events;

use DateTimeImmutable;
use Notion\Common\Date;
use Notion\Sessions\NumberOperator;
use Notion\Sessions\TimestampOperator;

/**
 * Filter used by `Notion\Sessions\Client::queryEvents()`.
 *
 * Compound filters (`and()` and `or()`) can be nested up to three levels deep.
 *
 * @psalm-immutable
 */
final readonly class EventFilter
{
    private function __construct(
        private array $filter,
    ) {
    }

    public static function id(string $eventId): self
    {
        return new self([
            "property" => "id",
            "string" => [ "equals" => $eventId ],
        ]);
    }

    /** Events with any of the given types. */
    public static function type(EventType $type, EventType ...$types): self
    {
        $condition = $types === []
            ? [ "equals" => $type->value ]
            : [ "in" => array_map(fn (EventType $t) => $t->value, [$type, ...$types]) ];

        return new self([
            "property" => "type",
            "event_type" => $condition,
        ]);
    }

    public static function sequence(NumberOperator $operator, int $sequence): self
    {
        return new self([
            "property" => "sequence",
            "number" => [ $operator->value => $sequence ],
        ]);
    }

    public static function createdAt(TimestampOperator $operator, DateTimeImmutable $date): self
    {
        return new self([
            "property" => "created_at",
            "timestamp" => [ $operator->value => $date->format(Date::FORMAT) ],
        ]);
    }

    /** Events that match every filter. */
    public static function and(self ...$filters): self
    {
        return new self([ "and" => array_map(fn (self $f) => $f->toArray(), $filters) ]);
    }

    /** Events that match any filter. */
    public static function or(self ...$filters): self
    {
        return new self([ "or" => array_map(fn (self $f) => $f->toArray(), $filters) ]);
    }

    /** @internal */
    public function toArray(): array
    {
        return $this->filter;
    }
}
