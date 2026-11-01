<?php

namespace Notion\Agents\Batch;

use Notion\Agents\AgentStatus;

/**
 * Single agent management operation of a batch
 *
 * @psalm-type OperationJson = array{
 *     action: "update_status"|"update_credit_limit"|"delete",
 *     agent_id: string,
 *     fields?: array{ status: "active"|"disabled" }|array{ credit_limit: int|null },
 * }
 *
 * @psalm-immutable
 */
final readonly class Operation
{
    private function __construct(
        public Action $action,
        public string $agentId,
        public AgentStatus|null $status,
        public int|null $creditLimit,
    ) {
    }

    public static function enable(string $agentId): self
    {
        return new self(Action::UpdateStatus, $agentId, AgentStatus::Active, null);
    }

    public static function disable(string $agentId): self
    {
        return new self(Action::UpdateStatus, $agentId, AgentStatus::Disabled, null);
    }

    public static function changeCreditLimit(string $agentId, int $creditLimit): self
    {
        return new self(Action::UpdateCreditLimit, $agentId, null, $creditLimit);
    }

    public static function removeCreditLimit(string $agentId): self
    {
        return new self(Action::UpdateCreditLimit, $agentId, null, null);
    }

    public static function delete(string $agentId): self
    {
        return new self(Action::Delete, $agentId, null, null);
    }

    /**
     * @internal
     *
     * @return OperationJson
     */
    public function toArray(): array
    {
        return match ($this->action) {
            Action::UpdateStatus => [
                "action" => $this->action->value,
                "agent_id" => $this->agentId,
                "fields" => [
                    "status" => $this->status === AgentStatus::Active ? "active" : "disabled",
                ],
            ],
            Action::UpdateCreditLimit => [
                "action" => $this->action->value,
                "agent_id" => $this->agentId,
                "fields" => [ "credit_limit" => $this->creditLimit ],
            ],
            Action::Delete => [
                "action" => $this->action->value,
                "agent_id" => $this->agentId,
            ],
        };
    }
}
