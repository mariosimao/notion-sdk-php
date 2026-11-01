<?php

namespace Notion\Test\Integration;

use Notion\Sessions\Events\EventFilter;
use Notion\Sessions\Events\EventQuery;
use Notion\Sessions\Events\EventType;
use Notion\Sessions\Events\UserMessage;
use Notion\Sessions\Message;
use Notion\Sessions\Query;
use Notion\Sessions\SessionFilter;
use Notion\Sessions\SessionStatus;
use PHPUnit\Framework\TestCase;

class SessionsTest extends TestCase
{
    public function test_start_find_query_and_cancel_session(): void
    {
        $agentId = Helper::testAgentId();
        if ($agentId === null) {
            $this->markTestSkipped("TEST_AGENT_ID is required to run Agent API session tests.");
        }

        $notion = Helper::client();

        $session = $notion->sessions()->send(Message::start($agentId, "Reply only with the word pong."));
        $this->assertSame($agentId, $session->agentId);

        $found = $notion->sessions()->find($session->id);
        $this->assertSame($session->id, $found->id);

        $result = $notion->sessions()->query(
            Query::create()
                ->changeFilter(SessionFilter::id($session->id))
                ->changePageSize(1),
        );
        $this->assertSame($session->id, $result->sessions[0]->id);

        $events = $notion->sessions()->queryAllEvents(
            $session->id,
            EventQuery::create()->changeFilter(EventFilter::type(EventType::UserMessage)),
        );
        $this->assertInstanceOf(UserMessage::class, $events[0]);
        $this->assertSame("Reply only with the word pong.", $events[0]->toString());

        $cancelled = $notion->sessions()->cancel($session->id);
        $this->assertSame($session->id, $cancelled->id);
        $this->assertContains($cancelled->status, [
            SessionStatus::Canceled,
            SessionStatus::Completed,
            SessionStatus::Failed,
            SessionStatus::Terminated,
        ]);
    }
}
