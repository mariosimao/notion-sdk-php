<?php

namespace Notion\Test\Unit\Agents;

use Notion\Agents\AgentType;
use Notion\Agents\Query;
use Notion\Agents\Query\Filter;
use Notion\Agents\Query\Result;
use Notion\Agents\Query\Sort;
use PHPUnit\Framework\TestCase;

class QueryTest extends TestCase
{
    public function test_empty_query(): void
    {
        $query = Query::create();

        $this->assertSame([], $query->toArray());
        $this->assertNull($query->query);
        $this->assertNull($query->filter);
        $this->assertSame([], $query->sorts);
        $this->assertNull($query->startCursor);
        $this->assertNull($query->pageSize);
        $this->assertFalse($query->verbose);
        $this->assertFalse($query->includeDeleted);
    }

    public function test_full_query(): void
    {
        $query = Query::create()
            ->changeQuery("support")
            ->changeFilter(Filter::agentType(AgentType::CustomAgent))
            ->changeSorts(Sort::lastRunAt()->ascending(), Sort::createdTime())
            ->changeStartCursor("cursor")
            ->changePageSize(50)
            ->withInstructions()
            ->includeDeleted();

        $this->assertSame([
            "query" => "support",
            "filter" => [
                "property" => "agent_type",
                "string" => [ "equals" => "custom_agent" ],
            ],
            "sorts" => [
                [ "property" => "last_run_at", "direction" => "ascending" ],
                [ "property" => "created_time", "direction" => "descending" ],
            ],
            "start_cursor" => "cursor",
            "page_size" => 50,
            "verbose" => true,
            "include_deleted" => true,
        ], $query->toArray());
    }

    public function test_result(): void
    {
        $result = Result::fromArray([
            "results" => [
                [
                    "object" => "agent",
                    "id" => "agent-id",
                    "agent_type" => "custom_agent",
                    "name" => "Agent",
                    "status" => "active",
                ],
            ],
            "has_more" => true,
            "next_cursor" => "next",
        ]);

        $this->assertCount(1, $result->agents);
        $this->assertSame("agent-id", $result->agents[0]->id);
        $this->assertTrue($result->hasMore);
        $this->assertSame("next", $result->nextCursor);
    }
}
