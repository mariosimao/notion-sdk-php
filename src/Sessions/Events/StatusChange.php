<?php

namespace Notion\Sessions\Events;

use DateTimeImmutable;
use Notion\Sessions\RequiredAction;
use Notion\Sessions\SessionError;
use Notion\Sessions\SessionStatus;

/**
 * The session moved to a new status.
 *
 * @psalm-import-type RequiredActionJson from RequiredAction
 * @psalm-import-type SessionErrorJson from SessionError
 * @psalm-import-type TokenUsageJson from TokenUsage
 * @psalm-import-type ArtifactJson from Artifact
 *
 * @psalm-type StatusChangeJson = array{
 *      object: "session_event",
 *      id: string,
 *      session_id: string,
 *      sequence?: int,
 *      created_at: string,
 *      type: "session.status",
 *      status: string,
 *      required_actions?: list<RequiredActionJson>,
 *      error?: SessionErrorJson,
 *      usage?: TokenUsageJson,
 *      artifacts?: list<ArtifactJson>,
 * }
 *
 * @psalm-immutable
 */
final readonly class StatusChange extends SessionEvent
{
    /**
     * @param list<RequiredAction> $requiredActions
     * @param list<Artifact> $artifacts
     */
    private function __construct(
        string $id,
        string $sessionId,
        int|null $sequence,
        DateTimeImmutable $createdAt,
        public SessionStatus $status,
        public array $requiredActions,
        public SessionError|null $error,
        public TokenUsage|null $usage,
        public array $artifacts,
    ) {
        parent::__construct($id, $sessionId, $sequence, $createdAt);
    }

    /**
     * @param StatusChangeJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        $error = $array["error"] ?? null;
        $usage = $array["usage"] ?? null;

        return new self(
            $array["id"],
            $array["session_id"],
            $array["sequence"] ?? null,
            new DateTimeImmutable($array["created_at"]),
            SessionStatus::from($array["status"]),
            RequiredAction::fromArrayList($array["required_actions"] ?? []),
            $error !== null ? SessionError::fromArray($error) : null,
            $usage !== null ? TokenUsage::fromArray($usage) : null,
            array_map(Artifact::fromArray(...), $array["artifacts"] ?? []),
        );
    }

    public function type(): EventType
    {
        return EventType::SessionStatus;
    }

    /** @return StatusChangeJson */
    public function toArray(): array
    {
        $array = $this->commonToArray();
        $array["type"] = "session.status";
        $array["status"] = $this->status->value;

        if ($this->requiredActions !== []) {
            $array["required_actions"] = array_map(fn (RequiredAction $a) => $a->toArray(), $this->requiredActions);
        }
        if ($this->error !== null) {
            $array["error"] = $this->error->toArray();
        }
        if ($this->usage !== null) {
            $array["usage"] = $this->usage->toArray();
        }
        if ($this->artifacts !== []) {
            $array["artifacts"] = array_map(fn (Artifact $a) => $a->toArray(), $this->artifacts);
        }

        return $array;
    }
}
