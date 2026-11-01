<?php

namespace Notion\Sessions\Events;

use DateTimeImmutable;
use Notion\Sessions\SessionCreator;

/**
 * A message sent to the agent.
 *
 * @psalm-import-type ContentJson from Content
 * @psalm-import-type SessionCreatorJson from SessionCreator
 *
 * @psalm-type UserMessageJson = array{
 *      object: "session_event",
 *      id: string,
 *      session_id: string,
 *      sequence?: int,
 *      created_at: string,
 *      type: "user.message",
 *      content: list<ContentJson>,
 *      created_by?: SessionCreatorJson|null,
 *      metadata?: array<string, string>|null,
 * }
 *
 * @psalm-immutable
 */
final readonly class UserMessage extends SessionEvent
{
    /**
     * @param list<Content> $content
     * @param array<string, string> $metadata
     */
    private function __construct(
        string $id,
        string $sessionId,
        int|null $sequence,
        DateTimeImmutable $createdAt,
        public array $content,
        public SessionCreator|null $createdBy,
        public array $metadata,
    ) {
        parent::__construct($id, $sessionId, $sequence, $createdAt);
    }

    /**
     * @param UserMessageJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        $createdBy = $array["created_by"] ?? null;

        return new self(
            $array["id"],
            $array["session_id"],
            $array["sequence"] ?? null,
            new DateTimeImmutable($array["created_at"]),
            Content::fromArrayList($array["content"]),
            $createdBy !== null ? SessionCreator::fromArray($createdBy) : null,
            $array["metadata"] ?? [],
        );
    }

    public function type(): EventType
    {
        return EventType::UserMessage;
    }

    /** The message text, ignoring attached files. */
    public function toString(): string
    {
        return Content::multipleToString($this->content);
    }

    /** @return UserMessageJson */
    public function toArray(): array
    {
        $array = $this->commonToArray();
        $array["type"] = "user.message";
        $array["content"] = array_map(fn (Content $c) => $c->toArray(), $this->content);
        $array["created_by"] = $this->createdBy?->toArray();
        $array["metadata"] = $this->metadata;

        return $array;
    }
}
