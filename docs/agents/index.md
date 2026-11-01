# Agents

## Introduction

Find the Custom Agents your token can access, read their usage insights, and
manage their status and credit limits. See the
[Notion Agent APIs overview](https://developers.notion.com/guides/notion-agent-apis/overview)
for access requirements.

::: warning
The Notion Agent APIs are in public beta.
:::

## Querying agents

```php
use Notion\Agents\AgentStatus;
use Notion\Agents\AgentType;
use Notion\Agents\Query;
use Notion\Agents\Query\Filter;
use Notion\Agents\Query\Sort;

$query = Query::create()
    ->changeQuery("support")                   // Search names and descriptions
    ->changeFilter(Filter::and(
        Filter::agentType(AgentType::CustomAgent),
        Filter::status(AgentStatus::Active, AgentStatus::Disabled),
    ))
    ->changeSorts(Sort::lastRunAt()->descending())
    ->changePageSize(20)
    ->withInstructions()                       // Include inline instructions
    ->includeDeleted();                        // Include soft-deleted agents

$result = $notion->agents()->query($query);

$result->agents;     // Agent[]
$result->hasMore;    // bool
$result->nextCursor; // string|null

// Fetch all pages of results
$agents = $notion->agents()->queryAll($query);
```

## Retrieving an agent

```php
use Notion\Agents\Agent;

$agent = $notion->agents()->find($agentId);
$agent = $notion->agents()->find($agentId, withInstructions: true);
$personal = $notion->agents()->find(Agent::PERSONAL_AGENT_ID); // Notion AI

$agent->name;
$agent->status;      // AgentStatus::Active|Disabled|Deleted
$agent->pauseReason; // PauseReason|null
$agent->creditLimit; // int|null or Hidden::Hidden without full access
```

Fields the integration cannot read are returned as `Notion\Agents\Hidden::Hidden`.

## Insights

```php
$insights = $notion->agents()->insights($agentId); // Current billing period
$insights = $notion->agents()->insights(
    $agentId,
    new DateTimeImmutable("2026-08-01"),
    new DateTimeImmutable("2026-09-01"),
);

$insights->totalCreditsUsed; // float
$insights->runsCompleted;    // int
```

## Managing agents

```php
$notion->agents()->disable($agentId);
$notion->agents()->enable($agentId);
$notion->agents()->changeCreditLimit($agentId, 500);
$notion->agents()->removeCreditLimit($agentId);
$notion->agents()->delete($agentId); // Soft-delete
```

## Batch operations

Batches are applied asynchronously. Each operation succeeds or fails on its own.

```php
use Notion\Agents\Batch\Operation;

$task = $notion->agents()->batch(
    Operation::disable($agentId1),
    Operation::changeCreditLimit($agentId2, 100),
    Operation::delete($agentId3),
);

$task = $notion->agents()->findBatch($task->id); // Poll once...
$task = $notion->agents()->waitForBatch($task);  // ... or until completed

if ($task->isSucceeded()) {
    foreach ($task->results as $result) {
        $result->index;     // Position in the batch
        $result->isError(); // bool
        $result->error;     // TaskError|null
    }
}
```
