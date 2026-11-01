<?php

namespace Notion\Agents;

use DateTimeImmutable;

/**
 * Result of changing or removing an agent credit limit
 *
 * @psalm-type AgentCreditLimitChangeJson = array{
 *     agent_id: string,
 *     credit_limit: int|null,
 *     last_edited_time: string,
 * }
 *
 * @psalm-immutable
 */
final readonly class AgentCreditLimitChange
{
    private function __construct(
        public string $agentId,
        public int|null $creditLimit,
        public DateTimeImmutable $lastEditedTime,
    ) {
    }

    /**
     * @param AgentCreditLimitChangeJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        return new self(
            $array["agent_id"],
            $array["credit_limit"],
            new DateTimeImmutable($array["last_edited_time"]),
        );
    }
}
