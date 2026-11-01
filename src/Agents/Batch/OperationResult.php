<?php

namespace Notion\Agents\Batch;

use DateTimeImmutable;
use Notion\Agents\AgentStatus;
use Notion\Agents\PauseReason;

/**
 * Outcome of a single operation of a batch
 *
 * @psalm-import-type TaskErrorJson from TaskError
 *
 * @psalm-type OperationResultJson = array{
 *     index: int,
 *     action: "update_status"|"update_credit_limit"|"delete",
 *     agent_id: string,
 *     outcome: string,
 *     status?: "active"|"disabled"|"deleted",
 *     pause_reason?: string|null,
 *     credit_limit?: int|null,
 *     last_edited_time?: string,
 *     deleted_at?: string,
 *     error?: TaskErrorJson,
 * }
 *
 * @psalm-immutable
 */
final readonly class OperationResult
{
    /** @param int $index Position of the operation in the batch request */
    private function __construct(
        public int $index,
        public Action $action,
        public string $agentId,
        public string $outcome,
        public AgentStatus|null $status,
        public PauseReason|null $pauseReason,
        public int|null $creditLimit,
        public DateTimeImmutable|null $lastEditedTime,
        public DateTimeImmutable|null $deletedAt,
        public TaskError|null $error,
    ) {
    }

    /**
     * @param OperationResultJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        $status = $array["status"] ?? null;
        $pauseReason = $array["pause_reason"] ?? null;

        return new self(
            $array["index"],
            Action::from($array["action"]),
            $array["agent_id"],
            $array["outcome"],
            $status !== null ? AgentStatus::from($status) : null,
            $pauseReason !== null ? PauseReason::from($pauseReason) : null,
            $array["credit_limit"] ?? null,
            isset($array["last_edited_time"]) ? new DateTimeImmutable($array["last_edited_time"]) : null,
            isset($array["deleted_at"]) ? new DateTimeImmutable($array["deleted_at"]) : null,
            isset($array["error"]) ? TaskError::fromArray($array["error"]) : null,
        );
    }

    public function isSuccess(): bool
    {
        return $this->error === null;
    }

    public function isError(): bool
    {
        return $this->error !== null;
    }
}
