<?php

namespace Notion\Test\Unit\Agents\Query;

use DateTimeImmutable;
use Notion\Agents\AgentModelMode;
use Notion\Agents\AgentStatus;
use Notion\Agents\Query\Filter;
use PHPUnit\Framework\TestCase;

class FilterTest extends TestCase
{
    public function test_id(): void
    {
        $this->assertSame(
            [ "property" => "id", "id" => [ "equals" => "agent-id" ] ],
            Filter::id("agent-id")->toArray(),
        );
    }

    public function test_created_by(): void
    {
        $this->assertSame(
            [ "property" => "created_by", "people" => [ "contains" => "user-id" ] ],
            Filter::createdBy("user-id")->toArray(),
        );
    }

    public function test_created_time(): void
    {
        $after = new DateTimeImmutable("2026-01-01T00:00:00+00:00");
        $before = new DateTimeImmutable("2026-02-01T00:00:00+00:00");

        $this->assertSame(
            [
                "property" => "created_time",
                "date" => [
                    "after" => "2026-01-01T00:00:00+00:00",
                    "before" => "2026-02-01T00:00:00+00:00",
                ],
            ],
            Filter::createdTime($after, $before)->toArray(),
        );
    }

    public function test_last_run_at(): void
    {
        $after = new DateTimeImmutable("2026-01-01T00:00:00+00:00");

        $this->assertSame(
            [ "property" => "last_run_at", "date" => [ "after" => "2026-01-01T00:00:00+00:00" ] ],
            Filter::lastRunAt(after: $after)->toArray(),
        );
    }

    public function test_favorited(): void
    {
        $this->assertSame(
            [ "property" => "favorited", "checkbox" => [ "equals" => true ] ],
            Filter::favorited()->toArray(),
        );
    }

    public function test_mcp_server(): void
    {
        $this->assertSame(
            [ "property" => "connections", "mcp_server" => [ "contains" => "linear" ] ],
            Filter::mcpServer("linear")->toArray(),
        );
    }

    public function test_status(): void
    {
        $this->assertSame(
            [ "property" => "status", "status" => [ "in" => [ "active", "disabled" ] ] ],
            Filter::status(AgentStatus::Active, AgentStatus::Disabled)->toArray(),
        );
    }

    public function test_model_mode(): void
    {
        $this->assertSame(
            [ "property" => "model_mode", "select" => [ "equals" => "auto" ] ],
            Filter::modelMode(AgentModelMode::Auto)->toArray(),
        );
    }

    public function test_agent_version(): void
    {
        $this->assertSame(
            [ "property" => "agent_version", "number" => [ "equals" => 2 ] ],
            Filter::agentVersion(2)->toArray(),
        );
    }

    public function test_compound(): void
    {
        $filter = Filter::and(
            Filter::favorited(false),
            Filter::or(Filter::id("a"), Filter::id("b")),
        );

        $this->assertSame(
            [
                "and" => [
                    [ "property" => "favorited", "checkbox" => [ "equals" => false ] ],
                    [
                        "or" => [
                            [ "property" => "id", "id" => [ "equals" => "a" ] ],
                            [ "property" => "id", "id" => [ "equals" => "b" ] ],
                        ],
                    ],
                ],
            ],
            $filter->toArray(),
        );
    }
}
