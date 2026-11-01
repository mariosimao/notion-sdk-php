<?php

namespace Notion\Test\Unit\Agents;

use Notion\Agents\AgentCreatorType;
use Notion\Agents\AgentInsights;
use Notion\Agents\AgentStatus;
use Notion\Agents\AgentType;
use Notion\Agents\Hidden;
use Notion\Agents\PauseReason;
use PHPUnit\Framework\TestCase;

class AgentInsightsTest extends TestCase
{
    public function test_from_array(): void
    {
        $insights = AgentInsights::fromArray([
            "object" => "agent_insights",
            "id" => "agent-id",
            "name" => "Agent",
            "agent_type" => "custom_agent",
            "status" => "disabled",
            "pause_reason" => "disabled_from_api",
            "created_by" => [ "id" => "bot-id", "type" => "bot" ],
            "total_credits_used" => 12.45,
            "credit_limit" => 100,
            "runs_completed" => 7,
        ]);

        $this->assertSame("agent-id", $insights->agentId);
        $this->assertSame("Agent", $insights->name);
        $this->assertSame(AgentType::CustomAgent, $insights->type);
        $this->assertSame(AgentStatus::Disabled, $insights->status);
        $this->assertSame(PauseReason::DisabledFromApi, $insights->pauseReason);
        $this->assertSame("bot-id", $insights->createdBy?->id);
        $this->assertSame(AgentCreatorType::Bot, $insights->createdBy?->type);
        $this->assertSame(12.45, $insights->totalCreditsUsed);
        $this->assertSame(100, $insights->creditLimit);
        $this->assertSame(7, $insights->runsCompleted);
    }

    public function test_hidden_credit_limit(): void
    {
        $insights = AgentInsights::fromArray([
            "object" => "agent_insights",
            "id" => "notion_ai",
            "name" => "Notion AI",
            "agent_type" => "notion_ai",
            "status" => "active",
            "pause_reason" => null,
            "created_by" => null,
            "total_credits_used" => 12,
            "credit_limit" => "hidden",
            "runs_completed" => 0,
        ]);

        $this->assertSame(Hidden::Hidden, $insights->creditLimit);
        $this->assertNull($insights->pauseReason);
        $this->assertNull($insights->createdBy);
        $this->assertSame(12.0, $insights->totalCreditsUsed);
    }
}
