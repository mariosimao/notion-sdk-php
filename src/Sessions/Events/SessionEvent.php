<?php

namespace Notion\Sessions\Events;

use DateTimeImmutable;
use Notion\Common\Date;

/**
 * Something that happened in a session: a message, thinking, a tool call or a
 * status transition.
 *
 * Events received from `Notion\Sessions\Client::queryEvents()` and committed
 * stream events are durable and have a `sequence`. Provisional stream events
 * have no `sequence` and may be superseded.
 *
 * @psalm-type SessionEventJson = array{
 *      object: "session_event",
 *      id: string,
 *      session_id: string,
 *      sequence?: int,
 *      created_at: string,
 *      type: string,
 *      ...
 * }
 *
 * @psalm-immutable
 */
abstract readonly class SessionEvent
{
    protected function __construct(
        public string $id,
        public string $sessionId,
        public int|null $sequence,
        public DateTimeImmutable $createdAt,
    ) {
    }

    abstract public function type(): EventType;

    /** @return SessionEventJson */
    abstract public function toArray(): array;

    /**
     * Provisional events are output the agent is still producing. They should
     * not be persisted as they may be superseded by a committed event.
     */
    public function isProvisional(): bool
    {
        return $this->sequence === null;
    }

    /**
     * @return array{
     *      object: "session_event",
     *      id: string,
     *      session_id: string,
     *      sequence?: int,
     *      created_at: string,
     *      type: string,
     * }
     */
    protected function commonToArray(): array
    {
        $array = [
            "object" => "session_event",
            "id" => $this->id,
            "session_id" => $this->sessionId,
            "created_at" => $this->createdAt->format(Date::FORMAT),
            "type" => $this->type()->value,
        ];

        if ($this->sequence !== null) {
            $array["sequence"] = $this->sequence;
        }

        return $array;
    }
}
