<?php

namespace Notion\Test\Integration;

use Notion\Agents\Agent;
use Notion\Agents\Batch\Operation;
use Notion\Agents\Query;
use Notion\Agents\Query\Filter;
use Notion\Agents\Query\Sort;
use Notion\Exceptions\ApiException;
use PHPUnit\Framework\TestCase;

class AgentsTest extends TestCase
{
    public function test_query_agents(): void
    {
        $client = Helper::client();

        $query = Query::create()
            ->changeSorts(Sort::createdTime()->descending())
            ->changePageSize(5);
        $result = $client->agents()->query($query);

        $this->assertLessThanOrEqual(5, count($result->agents));
        $this->assertContainsOnlyInstancesOf(Agent::class, $result->agents);
    }

    public function test_query_all_agents(): void
    {
        $client = Helper::client();

        $agents = $client->agents()->queryAll();

        $this->assertContainsOnlyInstancesOf(Agent::class, $agents);
    }

    public function test_find_agent_and_insights(): void
    {
        $client = Helper::client();

        $agents = $client->agents()->query(Query::create()->changePageSize(1))->agents;
        if ($agents === []) {
            $this->markTestSkipped("No Custom Agent is shared with the integration.");
        }

        $agent = $client->agents()->find($agents[0]->id);
        $this->assertSame($agents[0]->id, $agent->id);

        $filtered = $client->agents()->query(
            Query::create()->changeFilter(Filter::id($agent->id)),
        );
        $this->assertSame($agent->id, $filtered->agents[0]->id ?? null);

        $insights = $client->agents()->insights($agent->id);
        $this->assertSame($agent->id, $insights->agentId);
        $this->assertGreaterThanOrEqual(0, $insights->runsCompleted);
    }

    public function test_find_inexistent_agent(): void
    {
        $client = Helper::client();

        $this->expectException(ApiException::class);
        $client->agents()->find("7c3bd31e-63fa-4c60-956d-2264ceb2c522");
    }

    public function test_batch_with_inexistent_agent(): void
    {
        $client = Helper::client();

        $task = $client->agents()->batch(
            Operation::delete("7c3bd31e-63fa-4c60-956d-2264ceb2c522"),
        );
        $task = $client->agents()->waitForBatch($task);

        $this->assertTrue($task->isCompleted());
        if ($task->isSucceeded()) {
            $results = $task->results ?? [];
            $this->assertCount(1, $results);
            $this->assertTrue($results[0]->isError());
        }
    }
}
