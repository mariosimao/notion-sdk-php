<?php

namespace Notion\Agents;

use DateTimeImmutable;
use Notion\Agents\Batch\Operation;
use Notion\Agents\Batch\Task;
use Notion\Agents\Query\Result;
use Notion\Configuration;
use Notion\Infrastructure\Http;

/**
 * @psalm-import-type AgentJson from Agent
 * @psalm-import-type AgentInsightsJson from AgentInsights
 * @psalm-import-type AgentStatusChangeJson from AgentStatusChange
 * @psalm-import-type AgentCreditLimitChangeJson from AgentCreditLimitChange
 * @psalm-import-type AgentDeletionJson from AgentDeletion
 * @psalm-import-type AgentQueryResultJson from Result
 * @psalm-import-type TaskJson from Task
 */
final readonly class Client
{
    /**
     * @internal Use `\Notion\Notion::agents()` instead
     */
    public function __construct(
        private Configuration $config,
    ) {
    }

    /**
     * Query accessible agents
     */
    public function query(Query|null $query = null): Result
    {
        $data = ($query ?? Query::create())->toArray();

        $url = "https://api.notion.com/v1/agents/query";
        $request = Http::createRequest($url, $this->config)
            ->withMethod("POST")
            ->withHeader("Content-Type", "application/json");
        $request->getBody()->write(json_encode((object) $data));

        /** @psalm-var AgentQueryResultJson $body */
        $body = Http::sendRequest($request, $this->config);

        return Result::fromArray($body);
    }

    /**
     * Query accessible agents, fetching all result pages
     *
     * @return list<Agent>
     */
    public function queryAll(Query|null $query = null): array
    {
        $query = ($query ?? Query::create())->changePageSize(Query::MAX_PAGE_SIZE);

        $agents = [];
        do {
            $result = $this->query($query);
            $agents = [ ...$agents, ...$result->agents ];
            $query = $query->changeStartCursor($result->nextCursor);
        } while ($result->hasMore && $result->nextCursor !== null);

        return $agents;
    }

    /**
     * Retrieve an agent
     *
     * @param string $agentId Agent ID or `Agent::PERSONAL_AGENT_ID` for the personal agent
     * @param bool $withInstructions Include the agent inline instructions
     */
    public function find(string $agentId, bool $withInstructions = false): Agent
    {
        $url = "https://api.notion.com/v1/agents/{$agentId}";
        if ($withInstructions) {
            $url .= "?verbose=true";
        }
        $request = Http::createRequest($url, $this->config);

        /** @psalm-var AgentJson $body */
        $body = Http::sendRequest($request, $this->config);

        return Agent::fromArray($body);
    }

    /**
     * Retrieve configuration and usage insights of an agent
     *
     * The window defaults to the current billing period when `$start` or `$end`
     * are omitted.
     *
     * @param string $agentId Agent ID or `Agent::PERSONAL_AGENT_ID` for the personal agent
     */
    public function insights(
        string $agentId,
        DateTimeImmutable|null $start = null,
        DateTimeImmutable|null $end = null,
    ): AgentInsights {
        $url = "https://api.notion.com/v1/agents/{$agentId}/insights";
        if ($start !== null && $end !== null) {
            $url .= "?" . http_build_query([
                "start_time" => $start->getTimestamp(),
                "end_time" => $end->getTimestamp(),
            ]);
        }
        $request = Http::createRequest($url, $this->config);

        /** @psalm-var AgentInsightsJson $body */
        $body = Http::sendRequest($request, $this->config);

        return AgentInsights::fromArray($body);
    }

    public function enable(string $agentId): AgentStatusChange
    {
        return $this->changeStatus($agentId, "active");
    }

    public function disable(string $agentId): AgentStatusChange
    {
        return $this->changeStatus($agentId, "disabled");
    }

    /** @param "active"|"disabled" $status */
    private function changeStatus(string $agentId, string $status): AgentStatusChange
    {
        $url = "https://api.notion.com/v1/agents/{$agentId}/status";
        $request = Http::createRequest($url, $this->config)
            ->withMethod("PATCH")
            ->withHeader("Content-Type", "application/json");
        $request->getBody()->write(json_encode([ "status" => $status ]));

        /** @psalm-var AgentStatusChangeJson $body */
        $body = Http::sendRequest($request, $this->config);

        return AgentStatusChange::fromArray($body);
    }

    public function changeCreditLimit(string $agentId, int $creditLimit): AgentCreditLimitChange
    {
        return $this->updateCreditLimit($agentId, $creditLimit);
    }

    public function removeCreditLimit(string $agentId): AgentCreditLimitChange
    {
        return $this->updateCreditLimit($agentId, null);
    }

    private function updateCreditLimit(string $agentId, int|null $creditLimit): AgentCreditLimitChange
    {
        $url = "https://api.notion.com/v1/agents/{$agentId}/credit_limit";
        $request = Http::createRequest($url, $this->config)
            ->withMethod("PATCH")
            ->withHeader("Content-Type", "application/json");
        $request->getBody()->write(json_encode([ "credit_limit" => $creditLimit ]));

        /** @psalm-var AgentCreditLimitChangeJson $body */
        $body = Http::sendRequest($request, $this->config);

        return AgentCreditLimitChange::fromArray($body);
    }

    /**
     * Soft-delete a Custom Agent
     */
    public function delete(string $agentId): AgentDeletion
    {
        $url = "https://api.notion.com/v1/agents/{$agentId}";
        $request = Http::createRequest($url, $this->config)
            ->withMethod("DELETE");

        /** @psalm-var AgentDeletionJson $body */
        $body = Http::sendRequest($request, $this->config);

        return AgentDeletion::fromArray($body);
    }

    /**
     * Asynchronously apply multiple agent management operations
     *
     * Use `findBatch()` or `waitForBatch()` to retrieve the operation results.
     */
    public function batch(Operation $operation, Operation ...$operations): Task
    {
        $data = [
            "operations" => array_map(
                fn(Operation $o): array => $o->toArray(),
                [ $operation, ...$operations ],
            ),
        ];

        $url = "https://api.notion.com/v1/agents/batch";
        $request = Http::createRequest($url, $this->config)
            ->withMethod("POST")
            ->withHeader("Content-Type", "application/json");
        $request->getBody()->write(json_encode($data));

        /** @psalm-var TaskJson $body */
        $body = Http::sendRequest($request, $this->config);

        return Task::fromArray($body);
    }

    /**
     * Retrieve the current state of a batch task
     */
    public function findBatch(string $taskId): Task
    {
        $url = "https://api.notion.com/v1/async_tasks/{$taskId}";
        $request = Http::createRequest($url, $this->config);

        /** @psalm-var TaskJson $body */
        $body = Http::sendRequest($request, $this->config);

        return Task::fromArray($body);
    }

    /**
     * Poll a batch task until it completes
     *
     * Waits `poll_after_seconds` between attempts. Returns the latest task state
     * when it does not complete within `$maxAttempts`.
     */
    public function waitForBatch(Task $task, int $maxAttempts = 30): Task
    {
        $attempts = 0;
        while (!$task->isCompleted() && $attempts < $maxAttempts) {
            sleep(max(1, $task->pollAfterSeconds ?? 1));
            $task = $this->findBatch($task->id);
            $attempts++;
        }

        return $task;
    }
}
