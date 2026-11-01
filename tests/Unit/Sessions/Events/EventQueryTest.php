<?php

namespace Notion\Test\Unit\Sessions\Events;

use DateTimeImmutable;
use Notion\Sessions\Events\EventFilter;
use Notion\Sessions\Events\EventQuery;
use Notion\Sessions\Events\EventQueryResult;
use Notion\Sessions\Events\EventType;
use Notion\Sessions\NumberOperator;
use Notion\Sessions\Sort;
use Notion\Sessions\TimestampOperator;
use PHPUnit\Framework\TestCase;

class EventQueryTest extends TestCase
{
    public function test_empty_query(): void
    {
        $this->assertSame([], EventQuery::create()->toArray());
    }

    public function test_full_query(): void
    {
        $query = EventQuery::create()
            ->changeFilter(EventFilter::or(
                EventFilter::type(EventType::AgentMessage),
                EventFilter::and(
                    EventFilter::type(EventType::UserMessage, EventType::SessionStatus),
                    EventFilter::sequence(NumberOperator::GreaterThan, 10),
                    EventFilter::createdAt(
                        TimestampOperator::Equals,
                        new DateTimeImmutable("2026-08-01T00:00:00.000000Z"),
                    ),
                ),
                EventFilter::id("event-id"),
            ))
            ->changeSorts(Sort::sequence()->ascending())
            ->changeStartCursor("cursor")
            ->changePageSize(50);

        $this->assertSame([
            "filter" => [
                "or" => [
                    [ "property" => "type", "event_type" => [ "equals" => "agent.message" ] ],
                    [
                        "and" => [
                            [ "property" => "type", "event_type" => [ "in" => ["user.message", "session.status"] ] ],
                            [ "property" => "sequence", "number" => [ "greater_than" => 10 ] ],
                            [
                                "property" => "created_at",
                                "timestamp" => [ "equals" => "2026-08-01T00:00:00.000000Z" ],
                            ],
                        ],
                    ],
                    [ "property" => "id", "string" => [ "equals" => "event-id" ] ],
                ],
            ],
            "sorts" => [ [ "property" => "sequence", "direction" => "ascending" ] ],
            "start_cursor" => "cursor",
            "page_size" => 50,
        ], $query->toArray());
    }

    public function test_result(): void
    {
        $result = EventQueryResult::fromArray([
            "object" => "list",
            "results" => [
                [
                    "object" => "session_event",
                    "id" => "event-id",
                    "session_id" => "session-id",
                    "sequence" => 1,
                    "created_at" => "2026-08-14T12:10:04.000Z",
                    "type" => "agent.tool_use",
                    "tool_name" => "search",
                ],
            ],
            "next_cursor" => null,
            "has_more" => false,
        ]);

        $this->assertCount(1, $result->events);
        $this->assertNull($result->nextCursor);
        $this->assertFalse($result->hasMore);
    }
}
