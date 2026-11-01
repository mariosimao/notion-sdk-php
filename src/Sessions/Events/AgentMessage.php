<?php

namespace Notion\Sessions\Events;

use DateTimeImmutable;
use Notion\Sessions\SessionCreator;

/**
 * A reply from the agent.
 *
 * @psalm-import-type ContentJson from Content
 * @psalm-import-type SessionCreatorJson from SessionCreator
 *
 * @psalm-type AgentMessageJson = array{
 *      object: "session_event",
 *      id: string,
 *      session_id: string,
 *      sequence?: int,
 *      created_at: string,
 *      type: "agent.message",
 *      content: list<ContentJson>,
 *      created_by?: SessionCreatorJson|null,
 *      metadata?: array{ model: string }|null,
 * }
 *
 * @psalm-immutable
 */
final readonly class AgentMessage extends SessionEvent
{
    /** @param list<Content> $content */
    private function __construct(
        string $id,
        string $sessionId,
        int|null $sequence,
        DateTimeImmutable $createdAt,
        public array $content,
        public SessionCreator|null $createdBy,
        public string|null $model,
    ) {
        parent::__construct($id, $sessionId, $sequence, $createdAt);
    }

    /**
     * @param AgentMessageJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        $createdBy = $array["created_by"] ?? null;
        $metadata = $array["metadata"] ?? null;

        return new self(
            $array["id"],
            $array["session_id"],
            $array["sequence"] ?? null,
            new DateTimeImmutable($array["created_at"]),
            Content::fromArrayList($array["content"]),
            $createdBy !== null ? SessionCreator::fromArray($createdBy) : null,
            $metadata !== null ? $metadata["model"] : null,
        );
    }

    public function type(): EventType
    {
        return EventType::AgentMessage;
    }

    /** The reply text, ignoring attached files. */
    public function toString(): string
    {
        return Content::multipleToString($this->content);
    }

    /** @return AgentMessageJson */
    public function toArray(): array
    {
        $array = $this->commonToArray();
        $array["type"] = "agent.message";
        $array["content"] = array_map(fn (Content $c) => $c->toArray(), $this->content);
        $array["created_by"] = $this->createdBy?->toArray();
        $array["metadata"] = $this->model !== null ? [ "model" => $this->model ] : null;

        return $array;
    }
}
