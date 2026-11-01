<?php

namespace Notion\Sessions\Events;

use DateTimeImmutable;

/**
 * A tool call finished. Tool outputs are not exposed by the API.
 *
 * @psalm-type AgentToolResultJson = array{
 *      object: "session_event",
 *      id: string,
 *      session_id: string,
 *      sequence?: int,
 *      created_at: string,
 *      type: "agent.tool_result",
 *      tool_use_id: string,
 *      tool_name: string,
 *      is_error: bool,
 * }
 *
 * @psalm-immutable
 */
final readonly class AgentToolResult extends SessionEvent
{
    private function __construct(
        string $id,
        string $sessionId,
        int|null $sequence,
        DateTimeImmutable $createdAt,
        public string $toolUseId,
        public string $toolName,
        public bool $isError,
    ) {
        parent::__construct($id, $sessionId, $sequence, $createdAt);
    }

    /**
     * @param AgentToolResultJson $array
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
            $array["tool_use_id"],
            $array["tool_name"],
            $array["is_error"],
        );
    }

    public function type(): EventType
    {
        return EventType::AgentToolResult;
    }

    /** @return AgentToolResultJson */
    public function toArray(): array
    {
        $array = $this->commonToArray();
        $array["type"] = "agent.tool_result";
        $array["tool_use_id"] = $this->toolUseId;
        $array["tool_name"] = $this->toolName;
        $array["is_error"] = $this->isError;

        return $array;
    }
}
