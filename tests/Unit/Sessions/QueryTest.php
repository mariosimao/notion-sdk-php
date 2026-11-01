<?php

namespace Notion\Test\Unit\Sessions;

use DateTimeImmutable;
use Notion\Sessions\Query;
use Notion\Sessions\QueryResult;
use Notion\Sessions\SessionFilter;
use Notion\Sessions\SessionStatus;
use Notion\Sessions\Sort;
use Notion\Sessions\SortDirection;
use Notion\Sessions\SortProperty;
use Notion\Sessions\TimestampOperator;
use PHPUnit\Framework\TestCase;

class QueryTest extends TestCase
{
    public function test_empty_query(): void
    {
        $this->assertSame([], Query::create()->toArray());
    }

    public function test_full_query(): void
    {
        $date = new DateTimeImmutable("2026-08-01T00:00:00.000000Z");

        $query = Query::create()
            ->changeTitle("summary")
            ->changeFilter(SessionFilter::and(
                SessionFilter::agentId("agent-id"),
                SessionFilter::or(
                    SessionFilter::status(SessionStatus::Completed),
                    SessionFilter::status(SessionStatus::Failed, SessionStatus::Canceled),
                ),
                SessionFilter::createdAt(TimestampOperator::OnOrAfter, $date),
                SessionFilter::updatedAt(TimestampOperator::Before, $date),
                SessionFilter::id("session-id"),
            ))
            ->changeSorts(Sort::updatedAt()->descending(), Sort::createdAt()->ascending())
            ->changeStartCursor("cursor")
            ->changePageSize(10);

        $this->assertSame([
            "query" => "summary",
            "filter" => [
                "and" => [
                    [ "property" => "agent_id", "string" => [ "equals" => "agent-id" ] ],
                    [
                        "or" => [
                            [ "property" => "status", "status" => [ "equals" => "completed" ] ],
                            [ "property" => "status", "status" => [ "in" => ["failed", "canceled"] ] ],
                        ],
                    ],
                    [ "property" => "created_at", "timestamp" => [ "on_or_after" => "2026-08-01T00:00:00.000000Z" ] ],
                    [ "property" => "updated_at", "timestamp" => [ "before" => "2026-08-01T00:00:00.000000Z" ] ],
                    [ "property" => "id", "string" => [ "equals" => "session-id" ] ],
                ],
            ],
            "sorts" => [
                [ "property" => "updated_at", "direction" => "descending" ],
                [ "property" => "created_at", "direction" => "ascending" ],
            ],
            "start_cursor" => "cursor",
            "page_size" => 10,
        ], $query->toArray());
    }

    public function test_sort(): void
    {
        $sort = Sort::sequence();

        $this->assertSame(SortProperty::Sequence, $sort->property);
        $this->assertSame(SortDirection::Descending, $sort->direction);
        $this->assertSame(SortDirection::Ascending, $sort->ascending()->direction);
    }

    public function test_result(): void
    {
        $result = QueryResult::fromArray([
            "object" => "list",
            "results" => [
                [
                    "object" => "session",
                    "id" => "session-id",
                    "agent_id" => "agent-id",
                    "title" => "Title",
                    "status" => "completed",
                    "created_at" => "2026-08-14T12:10:00.000Z",
                    "updated_at" => "2026-08-14T12:10:04.000Z",
                ],
            ],
            "next_cursor" => "next",
            "has_more" => true,
        ]);

        $this->assertCount(1, $result->sessions);
        $this->assertSame("session-id", $result->sessions[0]->id);
        $this->assertSame("next", $result->nextCursor);
        $this->assertTrue($result->hasMore);
    }
}
