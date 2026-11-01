<?php

namespace Notion\Agents;

/**
 * Configuration and usage insights of an agent
 *
 * @psalm-import-type AgentCreatorJson from AgentCreator
 *
 * @psalm-type AgentInsightsJson = array{
 *     object: "agent_insights",
 *     id: string,
 *     name: string,
 *     agent_type: "notion_ai"|"custom_agent"|"autofill_custom_agent",
 *     status: "active"|"disabled"|"deleted",
 *     pause_reason: string|null,
 *     created_by: AgentCreatorJson|null,
 *     total_credits_used: int|float,
 *     credit_limit: int|"hidden"|null,
 *     runs_completed: int|float,
 * }
 *
 * @psalm-immutable
 */
final readonly class AgentInsights
{
    private function __construct(
        public string $agentId,
        public string $name,
        public AgentType $type,
        public AgentStatus $status,
        public PauseReason|null $pauseReason,
        public AgentCreator|null $createdBy,
        public float $totalCreditsUsed,
        public int|Hidden|null $creditLimit,
        public int $runsCompleted,
    ) {
    }

    /**
     * @param AgentInsightsJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        $creditLimit = $array["credit_limit"];
        if ($creditLimit === "hidden") {
            $creditLimit = Hidden::Hidden;
        }

        $pauseReason = $array["pause_reason"];
        $createdBy = $array["created_by"];

        return new self(
            $array["id"],
            $array["name"],
            AgentType::from($array["agent_type"]),
            AgentStatus::from($array["status"]),
            $pauseReason !== null ? PauseReason::from($pauseReason) : null,
            $createdBy !== null ? AgentCreator::fromArray($createdBy) : null,
            (float) $array["total_credits_used"],
            $creditLimit,
            (int) $array["runs_completed"],
        );
    }
}
