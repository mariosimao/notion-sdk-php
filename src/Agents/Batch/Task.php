<?php

namespace Notion\Agents\Batch;

use DateTimeImmutable;

/**
 * Asynchronous task created by a batch of agent management operations
 *
 * @psalm-import-type OperationResultJson from OperationResult
 * @psalm-import-type TaskErrorJson from TaskError
 *
 * @psalm-type TaskJson = array{
 *     object: "async_task",
 *     id: string,
 *     status: "queued"|"running"|"retrying"|"succeeded"|"failed",
 *     status_url: string,
 *     created_time: string,
 *     operation: array{ surface: string, name: string },
 *     poll_after_seconds?: int,
 *     result?: array{ object?: "agent_batch_result", results?: list<OperationResultJson> },
 *     error?: TaskErrorJson,
 * }
 *
 * @psalm-immutable
 */
final readonly class Task
{
    /** @param list<OperationResult>|null $results Available once the task succeeds */
    private function __construct(
        public string $id,
        public TaskStatus $status,
        public string $statusUrl,
        public DateTimeImmutable $createdTime,
        public string $operationName,
        public int|null $pollAfterSeconds,
        public array|null $results,
        public TaskError|null $error,
    ) {
    }

    /**
     * @param TaskJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        $results = null;
        if (isset($array["result"])) {
            $results = array_map(
                fn(array $r): OperationResult => OperationResult::fromArray($r),
                $array["result"]["results"] ?? [],
            );
        }

        return new self(
            $array["id"],
            TaskStatus::from($array["status"]),
            $array["status_url"],
            new DateTimeImmutable($array["created_time"]),
            $array["operation"]["name"],
            $array["poll_after_seconds"] ?? null,
            $results,
            isset($array["error"]) ? TaskError::fromArray($array["error"]) : null,
        );
    }

    /** Whether the task finished, either successfully or not */
    public function isCompleted(): bool
    {
        return $this->status === TaskStatus::Succeeded || $this->status === TaskStatus::Failed;
    }

    public function isSucceeded(): bool
    {
        return $this->status === TaskStatus::Succeeded;
    }

    public function isFailed(): bool
    {
        return $this->status === TaskStatus::Failed;
    }
}
