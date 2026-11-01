<?php

namespace Notion\Test\Unit\Sessions\Events;

use Notion\Sessions\Events\AgentMessage;
use Notion\Sessions\Events\AgentThinking;
use Notion\Sessions\Events\AgentToolResult;
use Notion\Sessions\Events\AgentToolUse;
use Notion\Sessions\Events\ArtifactType;
use Notion\Sessions\Events\ContentType;
use Notion\Sessions\Events\EventFactory;
use Notion\Sessions\Events\EventType;
use Notion\Sessions\Events\StatusChange;
use Notion\Sessions\Events\UnknownEvent;
use Notion\Sessions\Events\UserMessage;
use Notion\Sessions\SessionStatus;
use PHPUnit\Framework\TestCase;

class EventFactoryTest extends TestCase
{
    private const COMMON = [
        "object" => "session_event",
        "id" => "event-id",
        "session_id" => "session-id",
        "sequence" => 7,
        "created_at" => "2026-08-14T12:10:04.000000Z",
    ];

    public function test_user_message(): void
    {
        $array = [
            ...self::COMMON,
            "type" => "user.message",
            "content" => [
                [ "type" => "text", "text" => "Summarize " ],
                [ "type" => "text", "text" => "this file" ],
                [
                    "type" => "file",
                    "name" => "report.pdf",
                    "content_type" => "application/pdf",
                    "url" => "https://example.com/report.pdf",
                    "expiry_time" => "2026-08-14T13:10:04.000000Z",
                ],
            ],
            "created_by" => [ "id" => "user-id", "type" => "user" ],
            "metadata" => [ "source" => "slack" ],
        ];

        $event = EventFactory::fromArray($array);

        $this->assertInstanceOf(UserMessage::class, $event);
        $this->assertSame(EventType::UserMessage, $event->type());
        $this->assertSame("event-id", $event->id);
        $this->assertSame("session-id", $event->sessionId);
        $this->assertSame(7, $event->sequence);
        $this->assertFalse($event->isProvisional());
        $this->assertSame("Summarize this file", $event->toString());
        $this->assertSame(ContentType::File, $event->content[2]->type);
        $this->assertSame("report.pdf", $event->content[2]->name);
        $this->assertSame("application/pdf", $event->content[2]->contentType);
        $this->assertSame("user-id", $event->createdBy?->id);
        $this->assertSame([ "source" => "slack" ], $event->metadata);
        $this->assertEquals($array, $event->toArray());
    }

    public function test_agent_message(): void
    {
        $array = [
            ...self::COMMON,
            "type" => "agent.message",
            "content" => [ [ "type" => "text", "text" => "Done!" ] ],
            "created_by" => [ "id" => "agent-id", "type" => "bot" ],
            "metadata" => [ "model" => "claude-sonnet-5" ],
        ];

        $event = EventFactory::fromArray($array);

        $this->assertInstanceOf(AgentMessage::class, $event);
        $this->assertSame(EventType::AgentMessage, $event->type());
        $this->assertSame("Done!", $event->toString());
        $this->assertSame("claude-sonnet-5", $event->model);
        $this->assertEquals($array, $event->toArray());
    }

    public function test_provisional_agent_message(): void
    {
        $common = self::COMMON;
        unset($common["sequence"]);
        $event = EventFactory::fromArray([
            ...$common,
            "type" => "agent.message",
            "content" => [ [ "type" => "text", "text" => "Do" ] ],
        ]);

        $this->assertInstanceOf(AgentMessage::class, $event);
        $this->assertTrue($event->isProvisional());
        $this->assertNull($event->createdBy);
        $this->assertNull($event->model);
        $this->assertArrayNotHasKey("sequence", $event->toArray());
    }

    public function test_agent_thinking(): void
    {
        $array = [
            ...self::COMMON,
            "type" => "agent.thinking",
            "content" => [ [ "type" => "text", "text" => "Hmm" ] ],
        ];

        $event = EventFactory::fromArray($array);

        $this->assertInstanceOf(AgentThinking::class, $event);
        $this->assertSame(EventType::AgentThinking, $event->type());
        $this->assertSame("Hmm", $event->toString());
        $this->assertEquals($array, $event->toArray());
    }

    public function test_tool_use_and_result(): void
    {
        $use = [ ...self::COMMON, "type" => "agent.tool_use", "tool_name" => "search" ];
        $result = [
            ...self::COMMON,
            "type" => "agent.tool_result",
            "tool_use_id" => "tool-use-id",
            "tool_name" => "search",
            "is_error" => false,
        ];

        $useEvent = EventFactory::fromArray($use);
        $resultEvent = EventFactory::fromArray($result);

        $this->assertInstanceOf(AgentToolUse::class, $useEvent);
        $this->assertSame(EventType::AgentToolUse, $useEvent->type());
        $this->assertSame("search", $useEvent->toolName);
        $this->assertEquals($use, $useEvent->toArray());

        $this->assertInstanceOf(AgentToolResult::class, $resultEvent);
        $this->assertSame(EventType::AgentToolResult, $resultEvent->type());
        $this->assertSame("tool-use-id", $resultEvent->toolUseId);
        $this->assertFalse($resultEvent->isError);
        $this->assertEquals($result, $resultEvent->toArray());
    }

    public function test_status_change(): void
    {
        $array = [
            ...self::COMMON,
            "type" => "session.status",
            "status" => "requires_action",
            "required_actions" => [
                [
                    "action_id" => "action-id",
                    "title" => "Allow?",
                    "options" => [ [ "id" => "approve", "label" => "Yes" ] ],
                ],
            ],
            "error" => [ "code" => "x", "message" => "y", "retryable" => false ],
            "usage" => [ "total_tokens" => 10, "input_tokens" => 6, "output_tokens" => 4 ],
            "artifacts" => [
                [ "type" => "page", "url" => "https://notion.so/page", "title" => "Report" ],
                [ "type" => "html_artifact", "url" => "https://notion.so/a", "page_url" => "https://notion.so/p" ],
            ],
        ];

        $event = EventFactory::fromArray($array);

        $this->assertInstanceOf(StatusChange::class, $event);
        $this->assertSame(EventType::SessionStatus, $event->type());
        $this->assertSame(SessionStatus::RequiresAction, $event->status);
        $this->assertSame("action-id", $event->requiredActions[0]->actionId);
        $this->assertSame("x", $event->error?->code);
        $this->assertSame(10, $event->usage?->totalTokens);
        $this->assertSame(6, $event->usage?->inputTokens);
        $this->assertSame(4, $event->usage?->outputTokens);
        $this->assertSame(ArtifactType::Page, $event->artifacts[0]->type);
        $this->assertSame("Report", $event->artifacts[0]->title);
        $this->assertSame("https://notion.so/p", $event->artifacts[1]->pageUrl);
        $this->assertEquals($array, $event->toArray());
    }

    public function test_minimal_status_change(): void
    {
        $array = [ ...self::COMMON, "type" => "session.status", "status" => "completed" ];

        $event = EventFactory::fromArray($array);

        $this->assertInstanceOf(StatusChange::class, $event);
        $this->assertSame([], $event->requiredActions);
        $this->assertNull($event->usage);
        $this->assertEquals($array, $event->toArray());
    }

    public function test_unknown_event(): void
    {
        $array = [ ...self::COMMON, "type" => "agent.something_new", "foo" => "bar" ];

        $event = EventFactory::fromArray($array);

        $this->assertInstanceOf(UnknownEvent::class, $event);
        $this->assertSame(EventType::Unknown, $event->type());
        $this->assertSame("agent.something_new", $event->rawType);
        $this->assertSame($array, $event->toArray());
    }

    public function test_from_array_list(): void
    {
        $events = EventFactory::fromArrayList([
            [ ...self::COMMON, "type" => "agent.tool_use", "tool_name" => "search" ],
        ]);

        $this->assertCount(1, $events);
    }
}
