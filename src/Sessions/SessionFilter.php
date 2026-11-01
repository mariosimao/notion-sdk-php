<?php

namespace Notion\Sessions;

use DateTimeImmutable;
use Notion\Common\Date;

/**
 * Filter used by `Notion\Sessions\Client::query()`.
 *
 * Compound filters (`and()` and `or()`) can be nested up to two levels deep.
 *
 * @psalm-immutable
 */
final readonly class SessionFilter
{
    private function __construct(
        private array $filter,
    ) {
    }

    public static function id(string $sessionId): self
    {
        return new self([
            "property" => "id",
            "string" => [ "equals" => $sessionId ],
        ]);
    }

    public static function agentId(string $agentId): self
    {
        return new self([
            "property" => "agent_id",
            "string" => [ "equals" => $agentId ],
        ]);
    }

    /** Sessions with any of the given statuses. */
    public static function status(SessionStatus $status, SessionStatus ...$statuses): self
    {
        $condition = $statuses === []
            ? [ "equals" => $status->value ]
            : [ "in" => array_map(fn (SessionStatus $s) => $s->value, [$status, ...$statuses]) ];

        return new self([
            "property" => "status",
            "status" => $condition,
        ]);
    }

    /** `TimestampOperator::Equals` is not supported for sessions. */
    public static function createdAt(TimestampOperator $operator, DateTimeImmutable $date): self
    {
        return self::timestamp("created_at", $operator, $date);
    }

    /** `TimestampOperator::Equals` is not supported for sessions. */
    public static function updatedAt(TimestampOperator $operator, DateTimeImmutable $date): self
    {
        return self::timestamp("updated_at", $operator, $date);
    }

    /** Sessions that match every filter. */
    public static function and(self ...$filters): self
    {
        return new self([ "and" => array_map(fn (self $f) => $f->toArray(), $filters) ]);
    }

    /** Sessions that match any filter. */
    public static function or(self ...$filters): self
    {
        return new self([ "or" => array_map(fn (self $f) => $f->toArray(), $filters) ]);
    }

    /** @internal */
    public function toArray(): array
    {
        return $this->filter;
    }

    private static function timestamp(string $property, TimestampOperator $operator, DateTimeImmutable $date): self
    {
        return new self([
            "property" => $property,
            "timestamp" => [ $operator->value => $date->format(Date::FORMAT) ],
        ]);
    }
}
