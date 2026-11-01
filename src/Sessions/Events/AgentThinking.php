<?php

namespace Notion\Sessions\Events;

use DateTimeImmutable;

/**
 * The agent's reasoning while working on a turn.
 *
 * @psalm-import-type ContentJson from Content
 *
 * @psalm-type AgentThinkingJson = array{
 *      object: "session_event",
 *      id: string,
 *      session_id: string,
 *      sequence?: int,
 *      created_at: string,
 *      type: "agent.thinking",
 *      content: list<ContentJson>,
 * }
 *
 * @psalm-immutable
 */
final readonly class AgentThinking extends SessionEvent
{
    /** @param list<Content> $content */
    private function __construct(
        string $id,
        string $sessionId,
        int|null $sequence,
        DateTimeImmutable $createdAt,
        public array $content,
    ) {
        parent::__construct($id, $sessionId, $sequence, $createdAt);
    }

    /**
     * @param AgentThinkingJson $array
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
            Content::fromArrayList($array["content"]),
        );
    }

    public function type(): EventType
    {
        return EventType::AgentThinking;
    }

    public function toString(): string
    {
        return Content::multipleToString($this->content);
    }

    /** @return AgentThinkingJson */
    public function toArray(): array
    {
        $array = $this->commonToArray();
        $array["type"] = "agent.thinking";
        $array["content"] = array_map(fn (Content $c) => $c->toArray(), $this->content);

        return $array;
    }
}
