<?php

namespace Notion\Sessions;

use DateTimeImmutable;
use Notion\Common\Date;

/**
 * A conversation with a Custom Agent.
 *
 * Fields after `$error` are only returned by the retrieve and query endpoints.
 * They are `null` (or empty) when the API omits them.
 *
 * @psalm-import-type RequiredActionJson from RequiredAction
 * @psalm-import-type SessionErrorJson from SessionError
 * @psalm-import-type SessionCreatorJson from SessionCreator
 * @psalm-import-type AgentVersionJson from AgentVersion
 * @psalm-import-type ModelSelectionJson from ModelSelection
 *
 * @psalm-type SessionJson = array{
 *      object: "session",
 *      id: string,
 *      agent_id: string,
 *      title: string,
 *      status: string,
 *      created_at: string,
 *      updated_at: string,
 *      required_actions?: list<RequiredActionJson>,
 *      error?: SessionErrorJson,
 *      created_by?: SessionCreatorJson,
 *      agent_version?: AgentVersionJson|null,
 *      models?: ModelSelectionJson,
 *      trigger_type?: string,
 *      type_labels?: list<string>|null,
 *      chat_user_emails?: list<string>|null,
 *      tool_types?: list<string>|null,
 *      tool_call_count?: int|null,
 *      credits_used?: float|int|null,
 *      runs_completed?: int|null,
 *      message_count?: int|null,
 * }
 *
 * @psalm-immutable
 */
final readonly class Session
{
    /**
     * @param list<RequiredAction> $requiredActions
     * @param list<string>|null $typeLabels
     * @param list<string>|null $chatUserEmails
     * @param list<string>|null $toolTypes
     */
    private function __construct(
        public string $id,
        public string $agentId,
        public string $title,
        public SessionStatus $status,
        public DateTimeImmutable $createdAt,
        public DateTimeImmutable $updatedAt,
        public array $requiredActions,
        public SessionError|null $error,
        public SessionCreator|null $createdBy,
        public AgentVersion|null $agentVersion,
        public ModelSelection|null $models,
        public string|null $triggerType,
        public array|null $typeLabels,
        public array|null $chatUserEmails,
        public array|null $toolTypes,
        public int|null $toolCallCount,
        public float|null $creditsUsed,
        public int|null $runsCompleted,
        public int|null $messageCount,
    ) {
    }

    /**
     * @param SessionJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        $agentVersion = $array["agent_version"] ?? null;
        $models = $array["models"] ?? null;
        $error = $array["error"] ?? null;
        $createdBy = $array["created_by"] ?? null;
        $creditsUsed = $array["credits_used"] ?? null;

        return new self(
            $array["id"],
            $array["agent_id"],
            $array["title"],
            SessionStatus::from($array["status"]),
            new DateTimeImmutable($array["created_at"]),
            new DateTimeImmutable($array["updated_at"]),
            RequiredAction::fromArrayList($array["required_actions"] ?? []),
            $error !== null ? SessionError::fromArray($error) : null,
            $createdBy !== null ? SessionCreator::fromArray($createdBy) : null,
            $agentVersion !== null ? AgentVersion::fromArray($agentVersion) : null,
            $models !== null ? ModelSelection::fromArray($models) : null,
            $array["trigger_type"] ?? null,
            $array["type_labels"] ?? null,
            $array["chat_user_emails"] ?? null,
            $array["tool_types"] ?? null,
            $array["tool_call_count"] ?? null,
            $creditsUsed !== null ? (float) $creditsUsed : null,
            $array["runs_completed"] ?? null,
            $array["message_count"] ?? null,
        );
    }

    /** Whether the session is waiting on `submitActions()` to continue. */
    public function requiresAction(): bool
    {
        return $this->status === SessionStatus::RequiresAction;
    }

    /** @return SessionJson */
    public function toArray(): array
    {
        $array = [
            "object" => "session",
            "id" => $this->id,
            "agent_id" => $this->agentId,
            "title" => $this->title,
            "status" => $this->status->value,
            "created_at" => $this->createdAt->format(Date::FORMAT),
            "updated_at" => $this->updatedAt->format(Date::FORMAT),
            "required_actions" => array_map(fn (RequiredAction $a) => $a->toArray(), $this->requiredActions),
        ];

        $optional = [
            "error" => $this->error?->toArray(),
            "created_by" => $this->createdBy?->toArray(),
            "agent_version" => $this->agentVersion?->toArray(),
            "models" => $this->models?->toArray(),
            "trigger_type" => $this->triggerType,
            "type_labels" => $this->typeLabels,
            "chat_user_emails" => $this->chatUserEmails,
            "tool_types" => $this->toolTypes,
            "tool_call_count" => $this->toolCallCount,
            "credits_used" => $this->creditsUsed,
            "runs_completed" => $this->runsCompleted,
            "message_count" => $this->messageCount,
        ];

        /** @psalm-var SessionJson */
        return array_merge($array, array_filter($optional, fn ($v) => $v !== null));
    }
}
