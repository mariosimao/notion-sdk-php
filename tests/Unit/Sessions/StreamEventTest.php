<?php

namespace Notion\Test\Unit\Sessions;

use Notion\Sessions\Events\AgentMessage;
use Notion\Sessions\SessionStatus;
use Notion\Sessions\StreamEvent;
use Notion\Sessions\StreamEventType;
use PHPUnit\Framework\TestCase;

class StreamEventTest extends TestCase
{
    public function test_snapshot(): void
    {
        $event = StreamEvent::fromArray([
            "type" => "session.snapshot",
            "session" => [
                "object" => "session",
                "id" => "session-id",
                "agent_id" => "agent-id",
                "title" => "",
                "status" => "queued",
                "created_at" => "2026-08-14T12:10:00.000Z",
                "updated_at" => "2026-08-14T12:10:00.000Z",
            ],
        ]);

        $this->assertSame(StreamEventType::SessionSnapshot, $event->type);
        $this->assertSame("session-id", $event->session?->id);
        $this->assertSame("session-id", $event->sessionId);
        $this->assertFalse($event->isFinal());
    }

    public function test_committed_event(): void
    {
        $event = StreamEvent::fromArray([
            "type" => "event.committed",
            "event" => [
                "object" => "session_event",
                "id" => "event-id",
                "session_id" => "session-id",
                "sequence" => 3,
                "created_at" => "2026-08-14T12:10:00.000Z",
                "type" => "agent.message",
                "content" => [ [ "type" => "text", "text" => "Hi" ] ],
            ],
        ]);

        $this->assertSame(StreamEventType::EventCommitted, $event->type);
        $this->assertInstanceOf(AgentMessage::class, $event->event);
        $this->assertSame("session-id", $event->sessionId);
    }

    public function test_end(): void
    {
        $event = StreamEvent::fromArray([
            "type" => "stream.end",
            "session_id" => "session-id",
            "status" => "requires_action",
        ]);

        $this->assertSame(StreamEventType::StreamEnd, $event->type);
        $this->assertSame(SessionStatus::RequiresAction, $event->status);
        $this->assertTrue($event->isFinal());
    }

    public function test_timeout_and_error(): void
    {
        $timeout = StreamEvent::fromArray([
            "type" => "stream.timeout",
            "session_id" => "session-id",
            "message" => "Reconnect",
        ]);
        $error = StreamEvent::fromArray([
            "type" => "stream.error",
            "error" => [ "code" => "internal", "message" => "Oops", "retryable" => true ],
        ]);
        $unknown = StreamEvent::fromArray([ "type" => "stream.something" ]);

        $this->assertSame("Reconnect", $timeout->message);
        $this->assertTrue($timeout->isFinal());
        $this->assertSame("internal", $error->error?->code);
        $this->assertNull($error->sessionId);
        $this->assertTrue($error->isFinal());
        $this->assertSame(StreamEventType::Unknown, $unknown->type);
        $this->assertFalse($unknown->isFinal());
    }
}
