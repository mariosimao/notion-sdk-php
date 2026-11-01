<?php

namespace Notion\Sessions\Events;

use DateTimeImmutable;

/**
 * An event type not yet supported by the SDK. The raw payload is available in
 * `$data` and the original type in `$rawType`.
 *
 * @psalm-import-type SessionEventJson from SessionEvent
 *
 * @psalm-immutable
 */
final readonly class UnknownEvent extends SessionEvent
{
    /** @param array<array-key, mixed> $data */
    private function __construct(
        string $id,
        string $sessionId,
        int|null $sequence,
        DateTimeImmutable $createdAt,
        public string $rawType,
        public array $data,
    ) {
        parent::__construct($id, $sessionId, $sequence, $createdAt);
    }

    /**
     * @param SessionEventJson $array
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
            $array["type"],
            $array,
        );
    }

    public function type(): EventType
    {
        return EventType::Unknown;
    }

    /** @return SessionEventJson */
    public function toArray(): array
    {
        /** @psalm-var SessionEventJson */
        return $this->data;
    }
}
