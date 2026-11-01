<?php

namespace Notion\Agents;

/**
 * @psalm-import-type AgentTriggerScheduleJson from AgentTriggerSchedule
 *
 * @psalm-type AgentTriggerJson = array{
 *     type: string,
 *     enabled: bool,
 *     schedule?: AgentTriggerScheduleJson,
 *     config?: array<string, mixed>,
 * }
 *
 * @psalm-immutable
 */
final readonly class AgentTrigger
{
    /**
     * @param string $type Machine trigger type, e.g. "notion.agent.mentioned", "recurrence"
     * @param array<string, mixed> $config Remaining per-type trigger configuration
     */
    private function __construct(
        public string $type,
        public bool $enabled,
        public AgentTriggerSchedule|null $schedule,
        public array $config,
    ) {
    }

    /**
     * @param AgentTriggerJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        return new self(
            $array["type"],
            $array["enabled"],
            isset($array["schedule"]) ? AgentTriggerSchedule::fromArray($array["schedule"]) : null,
            $array["config"] ?? [],
        );
    }
}
