<?php

namespace Notion\Test\Unit\Sessions;

use Notion\Sessions\ActionOption;
use Notion\Sessions\CreatorType;
use Notion\Sessions\ModelSelectionType;
use Notion\Sessions\Session;
use Notion\Sessions\SessionStatus;
use PHPUnit\Framework\TestCase;

class SessionTest extends TestCase
{
    public function test_from_minimal_array(): void
    {
        $array = [
            "object" => "session",
            "id" => "session-id",
            "agent_id" => "agent-id",
            "title" => "Weekly summary",
            "status" => "in_progress",
            "created_at" => "2026-08-14T12:10:00.000Z",
            "updated_at" => "2026-08-14T12:10:04.000Z",
        ];

        $session = Session::fromArray($array);

        $this->assertSame("session-id", $session->id);
        $this->assertSame("agent-id", $session->agentId);
        $this->assertSame("Weekly summary", $session->title);
        $this->assertSame(SessionStatus::InProgress, $session->status);
        $this->assertSame("2026-08-14", $session->createdAt->format("Y-m-d"));
        $this->assertSame([], $session->requiredActions);
        $this->assertNull($session->error);
        $this->assertNull($session->createdBy);
        $this->assertNull($session->models);
        $this->assertNull($session->creditsUsed);
        $this->assertFalse($session->requiresAction());

        $expected = $array;
        $expected["created_at"] = "2026-08-14T12:10:00.000000Z";
        $expected["updated_at"] = "2026-08-14T12:10:04.000000Z";
        $expected["required_actions"] = [];
        $this->assertEquals($expected, $session->toArray());
    }

    public function test_from_full_array(): void
    {
        $array = [
            "object" => "session",
            "id" => "session-id",
            "agent_id" => "agent-id",
            "title" => "Weekly summary",
            "status" => "requires_action",
            "created_at" => "2026-08-14T12:10:00.000000Z",
            "updated_at" => "2026-08-14T12:10:04.000000Z",
            "required_actions" => [
                [
                    "action_id" => "action-id",
                    "title" => "Allow editing the page?",
                    "options" => [
                        [ "id" => "approve", "label" => "Allow" ],
                        [ "id" => "reject", "label" => "Deny" ],
                    ],
                ],
            ],
            "error" => [ "code" => "internal", "message" => "Oops", "retryable" => true ],
            "created_by" => [ "id" => "user-id", "type" => "user" ],
            "agent_version" => [ "id" => "version-id", "number" => 3, "published_at" => "2026-08-01T00:00:00.000000Z" ],
            "models" => [ "type" => "pinned", "ids" => ["model-a", null] ],
            "trigger_type" => "api",
            "type_labels" => ["chat"],
            "chat_user_emails" => ["mario@example.com"],
            "tool_types" => ["search"],
            "tool_call_count" => 2,
            "credits_used" => 1.5,
            "runs_completed" => 1,
            "message_count" => 4,
        ];

        $session = Session::fromArray($array);

        $this->assertTrue($session->requiresAction());
        $this->assertSame("action-id", $session->requiredActions[0]->actionId);
        $this->assertSame("Allow editing the page?", $session->requiredActions[0]->title);
        $this->assertSame(ActionOption::Approve, $session->requiredActions[0]->options[0]->id);
        $this->assertSame("Deny", $session->requiredActions[0]->options[1]->label);
        $this->assertSame("internal", $session->error?->code);
        $this->assertSame("Oops", $session->error?->message);
        $this->assertTrue($session->error?->retryable);
        $this->assertSame(CreatorType::User, $session->createdBy?->type);
        $this->assertSame(3, $session->agentVersion?->number);
        $this->assertSame(ModelSelectionType::Pinned, $session->models?->type);
        $this->assertSame(["model-a", null], $session->models?->ids);
        $this->assertSame("api", $session->triggerType);
        $this->assertSame(["chat"], $session->typeLabels);
        $this->assertSame(["mario@example.com"], $session->chatUserEmails);
        $this->assertSame(["search"], $session->toolTypes);
        $this->assertSame(2, $session->toolCallCount);
        $this->assertSame(1.5, $session->creditsUsed);
        $this->assertSame(1, $session->runsCompleted);
        $this->assertSame(4, $session->messageCount);

        $this->assertEquals($array, $session->toArray());
    }

    public function test_auto_models(): void
    {
        $session = Session::fromArray([
            "object" => "session",
            "id" => "session-id",
            "agent_id" => "agent-id",
            "title" => "",
            "status" => "completed",
            "created_at" => "2026-08-14T12:10:00.000000Z",
            "updated_at" => "2026-08-14T12:10:04.000000Z",
            "agent_version" => null,
            "models" => [ "type" => "auto" ],
            "credits_used" => 2,
        ]);

        $this->assertNull($session->agentVersion);
        $this->assertSame(ModelSelectionType::Auto, $session->models?->type);
        $this->assertSame([ "type" => "auto" ], $session->models?->toArray());
        $this->assertSame(2.0, $session->creditsUsed);
    }

    public function test_terminal_statuses(): void
    {
        $this->assertTrue(SessionStatus::Completed->isTerminal());
        $this->assertTrue(SessionStatus::Failed->isTerminal());
        $this->assertTrue(SessionStatus::Canceled->isTerminal());
        $this->assertTrue(SessionStatus::Terminated->isTerminal());
        $this->assertFalse(SessionStatus::Queued->isTerminal());
        $this->assertFalse(SessionStatus::InProgress->isTerminal());
        $this->assertFalse(SessionStatus::RequiresAction->isTerminal());
    }
}
