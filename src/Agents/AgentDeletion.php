<?php

namespace Notion\Agents;

use DateTimeImmutable;

/**
 * Result of (soft) deleting an agent
 *
 * @psalm-type AgentDeletionJson = array{
 *     agent_id: string,
 *     status: "deleted",
 *     deleted_at: string,
 * }
 *
 * @psalm-immutable
 */
final readonly class AgentDeletion
{
    private function __construct(
        public string $agentId,
        public DateTimeImmutable $deletedAt,
    ) {
    }

    /**
     * @param AgentDeletionJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        return new self(
            $array["agent_id"],
            new DateTimeImmutable($array["deleted_at"]),
        );
    }
}
