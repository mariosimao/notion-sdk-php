<?php

namespace Notion\Agents;

use DateTimeImmutable;

/**
 * Result of enabling or disabling an agent
 *
 * @psalm-type AgentStatusChangeJson = array{
 *     agent_id: string,
 *     status: "active"|"disabled"|"deleted",
 *     pause_reason: string|null,
 *     last_edited_time: string,
 * }
 *
 * @psalm-immutable
 */
final readonly class AgentStatusChange
{
    private function __construct(
        public string $agentId,
        public AgentStatus $status,
        public PauseReason|null $pauseReason,
        public DateTimeImmutable $lastEditedTime,
    ) {
    }

    /**
     * @param AgentStatusChangeJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        $pauseReason = $array["pause_reason"];

        return new self(
            $array["agent_id"],
            AgentStatus::from($array["status"]),
            $pauseReason !== null ? PauseReason::from($pauseReason) : null,
            new DateTimeImmutable($array["last_edited_time"]),
        );
    }
}
