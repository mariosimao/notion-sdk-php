<?php

namespace Notion\Agents\Query;

use DateTimeImmutable;
use DateTimeInterface;
use Notion\Agents\AgentModelMode;
use Notion\Agents\AgentStatus;
use Notion\Agents\AgentType;

/**
 * Filter used to query agents
 *
 * Single conditions can be combined with `Filter::and()` and `Filter::or()`.
 *
 * @psalm-immutable
 */
final readonly class Filter
{
    /** @param array<string, mixed> $data */
    private function __construct(
        private array $data,
    ) {
    }

    public static function id(string $agentId): self
    {
        return new self([
            "property" => "id",
            "id" => [ "equals" => $agentId ],
        ]);
    }

    public static function agentType(AgentType $type): self
    {
        return new self([
            "property" => "agent_type",
            "string" => [ "equals" => $type->value ],
        ]);
    }

    public static function createdBy(string $userId): self
    {
        return new self([
            "property" => "created_by",
            "people" => [ "contains" => $userId ],
        ]);
    }

    public static function createdTime(
        DateTimeImmutable|null $after = null,
        DateTimeImmutable|null $before = null,
    ): self {
        return new self([
            "property" => "created_time",
            "date" => self::dateRange($after, $before),
        ]);
    }

    public static function lastRunAt(
        DateTimeImmutable|null $after = null,
        DateTimeImmutable|null $before = null,
    ): self {
        return new self([
            "property" => "last_run_at",
            "date" => self::dateRange($after, $before),
        ]);
    }

    public static function favorited(bool $favorited = true): self
    {
        return new self([
            "property" => "favorited",
            "checkbox" => [ "equals" => $favorited ],
        ]);
    }

    /** Agents connected to an MCP server whose host contains the given value */
    public static function mcpServer(string $contains): self
    {
        return new self([
            "property" => "connections",
            "mcp_server" => [ "contains" => $contains ],
        ]);
    }

    public static function status(AgentStatus $status, AgentStatus ...$statuses): self
    {
        $values = array_map(
            fn(AgentStatus $s): string => $s->value,
            [ $status, ...$statuses ],
        );

        return new self([
            "property" => "status",
            "status" => [ "in" => $values ],
        ]);
    }

    public static function modelMode(AgentModelMode $mode): self
    {
        return new self([
            "property" => "model_mode",
            "select" => [ "equals" => $mode->value ],
        ]);
    }

    public static function agentVersion(int $version): self
    {
        return new self([
            "property" => "agent_version",
            "number" => [ "equals" => $version ],
        ]);
    }

    public static function and(self $filter, self ...$filters): self
    {
        return new self([
            "and" => array_map(fn(self $f): array => $f->toArray(), [ $filter, ...$filters ]),
        ]);
    }

    public static function or(self $filter, self ...$filters): self
    {
        return new self([
            "or" => array_map(fn(self $f): array => $f->toArray(), [ $filter, ...$filters ]),
        ]);
    }

    /** @return array{ after?: string, before?: string } */
    private static function dateRange(DateTimeImmutable|null $after, DateTimeImmutable|null $before): array
    {
        $range = [];
        if ($after !== null) {
            $range["after"] = $after->format(DateTimeInterface::ATOM);
        }
        if ($before !== null) {
            $range["before"] = $before->format(DateTimeInterface::ATOM);
        }

        return $range;
    }

    /**
     * @internal
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->data;
    }
}
