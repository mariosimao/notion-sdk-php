<?php

namespace Notion\Test\Unit\Agents;

use DateTimeImmutable;
use Notion\Agents\AgentCreditLimitChange;
use Notion\Agents\AgentDeletion;
use Notion\Agents\AgentStatus;
use Notion\Agents\AgentStatusChange;
use Notion\Agents\PauseReason;
use PHPUnit\Framework\TestCase;

class AgentChangesTest extends TestCase
{
    public function test_status_change(): void
    {
        $change = AgentStatusChange::fromArray([
            "agent_id" => "agent-id",
            "status" => "disabled",
            "pause_reason" => "disabled_from_api",
            "last_edited_time" => "2026-08-20T10:00:00.000Z",
        ]);

        $this->assertSame("agent-id", $change->agentId);
        $this->assertSame(AgentStatus::Disabled, $change->status);
        $this->assertSame(PauseReason::DisabledFromApi, $change->pauseReason);
        $this->assertEquals(new DateTimeImmutable("2026-08-20T10:00:00.000Z"), $change->lastEditedTime);
    }

    public function test_status_change_without_pause_reason(): void
    {
        $change = AgentStatusChange::fromArray([
            "agent_id" => "agent-id",
            "status" => "active",
            "pause_reason" => null,
            "last_edited_time" => "2026-08-20T10:00:00.000Z",
        ]);

        $this->assertSame(AgentStatus::Active, $change->status);
        $this->assertNull($change->pauseReason);
    }

    public function test_credit_limit_change(): void
    {
        $change = AgentCreditLimitChange::fromArray([
            "agent_id" => "agent-id",
            "credit_limit" => 1000,
            "last_edited_time" => "2026-08-20T10:00:00.000Z",
        ]);

        $this->assertSame("agent-id", $change->agentId);
        $this->assertSame(1000, $change->creditLimit);
        $this->assertEquals(new DateTimeImmutable("2026-08-20T10:00:00.000Z"), $change->lastEditedTime);
    }

    public function test_deletion(): void
    {
        $deletion = AgentDeletion::fromArray([
            "agent_id" => "agent-id",
            "status" => "deleted",
            "deleted_at" => "2026-08-20T10:00:00.000Z",
        ]);

        $this->assertSame("agent-id", $deletion->agentId);
        $this->assertEquals(new DateTimeImmutable("2026-08-20T10:00:00.000Z"), $deletion->deletedAt);
    }
}
