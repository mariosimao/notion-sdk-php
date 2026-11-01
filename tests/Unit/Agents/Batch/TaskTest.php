<?php

namespace Notion\Test\Unit\Agents\Batch;

use DateTimeImmutable;
use Notion\Agents\AgentStatus;
use Notion\Agents\Batch\Action;
use Notion\Agents\Batch\Task;
use Notion\Agents\Batch\TaskStatus;
use Notion\Agents\PauseReason;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{
    public function test_pending_task(): void
    {
        $task = Task::fromArray([
            "object" => "async_task",
            "id" => "task_abc123",
            "status" => "queued",
            "status_url" => "https://api.notion.com/v1/async_tasks/task_abc123",
            "created_time" => "2026-08-20T12:00:00.000Z",
            "poll_after_seconds" => 2,
            "operation" => [ "surface" => "rest", "name" => "POST /v1/agents/batch" ],
        ]);

        $this->assertSame("task_abc123", $task->id);
        $this->assertSame(TaskStatus::Queued, $task->status);
        $this->assertSame("https://api.notion.com/v1/async_tasks/task_abc123", $task->statusUrl);
        $this->assertEquals(new DateTimeImmutable("2026-08-20T12:00:00.000Z"), $task->createdTime);
        $this->assertSame("POST /v1/agents/batch", $task->operationName);
        $this->assertSame(2, $task->pollAfterSeconds);
        $this->assertNull($task->results);
        $this->assertNull($task->error);
        $this->assertFalse($task->isCompleted());
        $this->assertFalse($task->isSucceeded());
        $this->assertFalse($task->isFailed());
    }

    public function test_succeeded_task(): void
    {
        $task = Task::fromArray([
            "object" => "async_task",
            "id" => "task_abc123",
            "status" => "succeeded",
            "status_url" => "https://api.notion.com/v1/async_tasks/task_abc123",
            "created_time" => "2026-08-20T12:00:00.000Z",
            "operation" => [ "surface" => "rest", "name" => "POST /v1/agents/batch" ],
            "result" => [
                "object" => "agent_batch_result",
                "results" => [
                    [
                        "index" => 0,
                        "action" => "update_status",
                        "agent_id" => "agent-1",
                        "outcome" => "success",
                        "status" => "disabled",
                        "pause_reason" => "disabled_from_api",
                        "last_edited_time" => "2026-08-20T12:00:01.000Z",
                    ],
                    [
                        "index" => 1,
                        "action" => "update_credit_limit",
                        "agent_id" => "agent-2",
                        "outcome" => "success",
                        "credit_limit" => 100,
                        "last_edited_time" => "2026-08-20T12:00:01.000Z",
                    ],
                    [
                        "index" => 2,
                        "action" => "delete",
                        "agent_id" => "agent-3",
                        "outcome" => "success",
                        "status" => "deleted",
                        "deleted_at" => "2026-08-20T12:00:01.000Z",
                    ],
                    [
                        "index" => 3,
                        "action" => "delete",
                        "agent_id" => "agent-4",
                        "outcome" => "error",
                        "error" => [
                            "object" => "error",
                            "status" => 404,
                            "code" => "object_not_found",
                            "message" => "Could not find agent.",
                        ],
                    ],
                ],
            ],
        ]);

        $this->assertTrue($task->isCompleted());
        $this->assertTrue($task->isSucceeded());
        $this->assertNull($task->pollAfterSeconds);

        $results = $task->results ?? [];
        $this->assertCount(4, $results);

        $this->assertSame(0, $results[0]->index);
        $this->assertSame(Action::UpdateStatus, $results[0]->action);
        $this->assertSame("agent-1", $results[0]->agentId);
        $this->assertSame("success", $results[0]->outcome);
        $this->assertSame(AgentStatus::Disabled, $results[0]->status);
        $this->assertSame(PauseReason::DisabledFromApi, $results[0]->pauseReason);
        $this->assertNotNull($results[0]->lastEditedTime);
        $this->assertTrue($results[0]->isSuccess());

        $this->assertSame(100, $results[1]->creditLimit);
        $this->assertSame(Action::UpdateCreditLimit, $results[1]->action);

        $this->assertSame(AgentStatus::Deleted, $results[2]->status);
        $this->assertEquals(new DateTimeImmutable("2026-08-20T12:00:01.000Z"), $results[2]->deletedAt);

        $this->assertTrue($results[3]->isError());
        $this->assertFalse($results[3]->isSuccess());
        $this->assertSame(404, $results[3]->error?->status);
        $this->assertSame("object_not_found", $results[3]->error?->code);
        $this->assertSame("Could not find agent.", $results[3]->error?->message);
    }

    public function test_failed_task(): void
    {
        $task = Task::fromArray([
            "object" => "async_task",
            "id" => "task_abc123",
            "status" => "failed",
            "status_url" => "https://api.notion.com/v1/async_tasks/task_abc123",
            "created_time" => "2026-08-20T12:00:00.000Z",
            "operation" => [ "surface" => "rest", "name" => "POST /v1/agents/batch" ],
            "error" => [
                "object" => "error",
                "status" => 500,
                "code" => "internal_server_error",
                "message" => "Unexpected error.",
            ],
        ]);

        $this->assertTrue($task->isCompleted());
        $this->assertTrue($task->isFailed());
        $this->assertNull($task->results);
        $this->assertSame("internal_server_error", $task->error?->code);
    }
}
