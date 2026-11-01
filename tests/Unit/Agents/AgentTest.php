<?php

namespace Notion\Test\Unit\Agents;

use Notion\Agents\Agent;
use Notion\Agents\AgentAvatar;
use Notion\Agents\AgentCreatorType;
use Notion\Agents\AgentModelMode;
use Notion\Agents\AgentStatus;
use Notion\Agents\AgentType;
use Notion\Agents\Hidden;
use Notion\Agents\PauseReason;
use Notion\Common\Icon;
use PHPUnit\Framework\TestCase;

class AgentTest extends TestCase
{
    public function test_from_array(): void
    {
        $agent = Agent::fromArray([
            "object" => "agent",
            "id" => "8b4e7c2a-1f3d-4a5b-9c6d-0e1f2a3b4c5d",
            "agent_type" => "custom_agent",
            "name" => "Support triage",
            "description" => "Triage support tickets",
            "instructions_page_id" => "5c1d2e3f-4a5b-6c7d-8e9f-0a1b2c3d4e5f",
            "icon" => [ "type" => "emoji", "emoji" => "🤖" ],
            "model" => [ "mode" => "pinned", "id" => "model-1" ],
            "connections" => [
                [
                    "type" => "notion",
                    "name" => "Notion",
                    "account" => null,
                    "permissions" => [
                        [
                            "target" => [ "type" => "page", "id" => "page-id" ],
                            "scopes" => [ "read", "write" ],
                        ],
                    ],
                ],
                [
                    "type" => "mcp_server",
                    "name" => "Linear",
                    "account" => [ "type" => "mcp_server", "server_host" => "mcp.linear.app" ],
                    "enabled_tools" => [
                        [ "name" => "create_issue", "title" => "Create issue" ],
                    ],
                    "run_tools_automatically" => [ "read" => true, "write" => false ],
                ],
                [
                    "type" => "slack",
                    "name" => "Slack",
                    "account" => "hidden",
                    "permissions" => [],
                ],
            ],
            "status" => "disabled",
            "pause_reason" => "credit_limit",
            "created_by" => [ "object" => "user", "type" => "user", "id" => "user-id" ],
            "version" => null,
            "agent_version" => [
                "id" => "version-id",
                "number" => 3,
                "published_at" => "2026-08-20T10:00:00.000Z",
            ],
            "has_unpublished_changes" => true,
            "last_run_time" => "2026-08-21T10:00:00.000Z",
            "last_run_at" => "2026-08-21T10:00:00.000Z",
            "credit_limit" => 500,
            "triggers" => [
                [
                    "type" => "recurrence",
                    "enabled" => true,
                    "schedule" => [
                        "frequency" => "week",
                        "interval" => 2,
                        "weekdays" => [ "monday" ],
                        "hour" => 9,
                        "minute" => 30,
                        "timezone" => "America/New_York",
                        "start_date" => "2026-08-01T00:00:00.000Z",
                        "end" => [ "type" => "count", "occurrences" => 10 ],
                    ],
                ],
                [
                    "type" => "slack.reaction.added",
                    "enabled" => false,
                    "config" => [ "channel_ids" => [ "C123" ] ],
                ],
            ],
            "is_favorited" => false,
            "created_time" => "2026-08-01T10:00:00.000Z",
            "last_edited_time" => "2026-08-02T10:00:00.000Z",
            "instructions" => null,
        ]);

        $this->assertSame("8b4e7c2a-1f3d-4a5b-9c6d-0e1f2a3b4c5d", $agent->id);
        $this->assertSame(AgentType::CustomAgent, $agent->type);
        $this->assertSame("Support triage", $agent->name);
        $this->assertSame("Triage support tickets", $agent->description);
        $this->assertSame("5c1d2e3f-4a5b-6c7d-8e9f-0a1b2c3d4e5f", $agent->instructionsPageId);
        $this->assertInstanceOf(Icon::class, $agent->icon);
        $this->assertTrue($agent->icon->isEmoji());
        $this->assertSame(AgentModelMode::Pinned, $agent->model?->mode);
        $this->assertTrue($agent->model?->isPinned());
        $this->assertSame("model-1", $agent->model->id);

        $this->assertCount(3, $agent->connections);
        $this->assertSame("notion", $agent->connections[0]->type);
        $this->assertNull($agent->connections[0]->account);
        $this->assertSame("page", $agent->connections[0]->permissions[0]->targetType);
        $this->assertSame("page-id", $agent->connections[0]->permissions[0]->target["id"]);
        $this->assertSame([ "read", "write" ], $agent->connections[0]->permissions[0]->scopes);
        $this->assertSame([ "create_issue" ], $agent->connections[1]->enabledTools);
        $this->assertTrue($agent->connections[1]->runReadToolsAutomatically);
        $this->assertFalse($agent->connections[1]->runWriteToolsAutomatically);
        $this->assertSame(Hidden::Hidden, $agent->connections[2]->account);

        $this->assertSame(AgentStatus::Disabled, $agent->status);
        $this->assertTrue($agent->isDisabled());
        $this->assertFalse($agent->isActive());
        $this->assertFalse($agent->isDeleted());
        $this->assertFalse($agent->isPersonalAgent());
        $this->assertSame(PauseReason::CreditLimit, $agent->pauseReason);
        $this->assertSame("user-id", $agent->createdBy?->id);
        $this->assertSame(AgentCreatorType::User, $agent->createdBy?->type);
        $this->assertSame(3, $agent->version?->number);
        $this->assertSame("version-id", $agent->version?->id);
        $this->assertTrue($agent->hasUnpublishedChanges);
        $this->assertEquals(new \DateTimeImmutable("2026-08-21T10:00:00.000Z"), $agent->lastRunTime);
        $this->assertSame(500, $agent->creditLimit);
        $this->assertTrue($agent->hasCreditLimit());

        $this->assertCount(2, $agent->triggers);
        $schedule = $agent->triggers[0]->schedule;
        $this->assertSame("week", $schedule?->frequency);
        $this->assertSame(2, $schedule?->interval);
        $this->assertSame([ "monday" ], $schedule?->weekdays);
        $this->assertSame(9, $schedule?->hour);
        $this->assertSame(30, $schedule?->minute);
        $this->assertSame("America/New_York", $schedule?->timezone);
        $this->assertSame(10, $schedule?->endOccurrences);
        $this->assertNull($schedule?->endAt);
        $this->assertNull($agent->triggers[1]->schedule);
        $this->assertFalse($agent->triggers[1]->enabled);
        $this->assertSame([ "channel_ids" => [ "C123" ] ], $agent->triggers[1]->config);

        $this->assertFalse($agent->isFavorited);
        $this->assertEquals(new \DateTimeImmutable("2026-08-01T10:00:00.000Z"), $agent->createdTime);
        $this->assertEquals(new \DateTimeImmutable("2026-08-02T10:00:00.000Z"), $agent->lastEditedTime);
        $this->assertNull($agent->instructions);
    }

    public function test_hidden_fields(): void
    {
        $agent = Agent::fromArray([
            "object" => "agent",
            "id" => "agent-id",
            "agent_type" => "custom_agent",
            "name" => "Agent",
            "icon" => [
                "type" => "custom_agent_avatar",
                "custom_agent_avatar" => [
                    "static_url" => "https://example.com/static.png",
                    "animated_url" => "https://example.com/animated.gif",
                ],
            ],
            "status" => "active",
            "pause_reason" => null,
            "has_unpublished_changes" => "hidden",
            "last_run_at" => "hidden",
            "credit_limit" => "hidden",
            "instructions" => "Be helpful",
        ]);

        $this->assertInstanceOf(AgentAvatar::class, $agent->icon);
        $this->assertSame("https://example.com/static.png", $agent->icon->staticUrl);
        $this->assertSame("https://example.com/animated.gif", $agent->icon->animatedUrl);
        $this->assertSame(Hidden::Hidden, $agent->hasUnpublishedChanges);
        $this->assertSame(Hidden::Hidden, $agent->lastRunTime);
        $this->assertSame(Hidden::Hidden, $agent->creditLimit);
        $this->assertFalse($agent->hasCreditLimit());
        $this->assertNull($agent->pauseReason);
        $this->assertNull($agent->model);
        $this->assertNull($agent->version);
        $this->assertSame([], $agent->connections);
        $this->assertSame([], $agent->triggers);
        $this->assertSame("Be helpful", $agent->instructions);
        $this->assertTrue($agent->isActive());
    }

    public function test_personal_agent(): void
    {
        $agent = Agent::fromArray([
            "object" => "agent",
            "id" => Agent::PERSONAL_AGENT_ID,
            "agent_type" => "notion_ai",
            "name" => "Notion AI",
            "icon" => null,
            "status" => "active",
            "last_run_at" => null,
            "credit_limit" => null,
        ]);

        $this->assertTrue($agent->isPersonalAgent());
        $this->assertNull($agent->icon);
        $this->assertNull($agent->lastRunTime);
        $this->assertNull($agent->creditLimit);
        $this->assertFalse($agent->hasCreditLimit());
    }

    public function test_external_file_icon(): void
    {
        $agent = Agent::fromArray([
            "object" => "agent",
            "id" => "agent-id",
            "agent_type" => "external",
            "name" => "External",
            "icon" => [ "type" => "external", "external" => [ "url" => "https://example.com/icon.png" ] ],
            "status" => "deleted",
        ]);

        $this->assertInstanceOf(Icon::class, $agent->icon);
        $this->assertTrue($agent->icon->isFile());
        $this->assertTrue($agent->isDeleted());
        $this->assertSame(AgentType::External, $agent->type);
    }

    public function test_unknown_icon_type_is_ignored(): void
    {
        $agent = Agent::fromArray([
            "object" => "agent",
            "id" => "agent-id",
            "agent_type" => "custom_agent",
            "name" => "Agent",
            "icon" => [ "type" => "unknown_icon" ],
            "status" => "active",
        ]);

        $this->assertNull($agent->icon);
    }
}
