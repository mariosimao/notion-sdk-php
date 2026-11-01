<?php

namespace Notion\Sessions\Events;

use DateTimeImmutable;

/**
 * The agent called a tool. Tool inputs are not exposed by the API.
 *
 * @psalm-type AgentToolUseJson = array{
 *      object: "session_event",
 *      id: string,
 *      session_id: string,
 *      sequence?: int,
 *      created_at: string,
 *      type: "agent.tool_use",
 *      tool_name: string,
 * }
 *
 * @psalm-immutable
 */
final readonly class AgentToolUse extends SessionEvent
{
    private function __construct(
        string $id,
        string $sessionId,
        int|null $sequence,
        DateTimeImmutable $createdAt,
        public string $toolName,
    ) {
        parent::__construct($id, $sessionId, $sequence, $createdAt);
    }

    /**
     * @param AgentToolUseJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        return new self(
            $array["id"],
            $array["session_id"],
            $array["sequence"] ?? null,
            new DateTimeImmutable($array["created_at"]),
            $array["tool_name"],
        );
    }

    public function type(): EventType
    {
        return EventType::AgentToolUse;
    }

    /** @return AgentToolUseJson */
    public function toArray(): array
    {
        $array = $this->commonToArray();
        $array["type"] = "agent.tool_use";
        $array["tool_name"] = $this->toolName;

        return $array;
    }
}
