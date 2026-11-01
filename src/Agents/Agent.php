<?php

namespace Notion\Agents;

use DateTimeImmutable;
use Notion\Common\Emoji;
use Notion\Common\File;
use Notion\Common\Icon;

/**
 * Custom Agent or personal agent (Notion AI)
 *
 * @psalm-import-type AgentModelJson from AgentModel
 * @psalm-import-type AgentConnectionJson from AgentConnection
 * @psalm-import-type AgentCreatorJson from AgentCreator
 * @psalm-import-type AgentVersionJson from AgentVersion
 * @psalm-import-type AgentTriggerJson from AgentTrigger
 * @psalm-import-type EmojiJson from \Notion\Common\Emoji
 * @psalm-import-type FileJson from \Notion\Common\File
 *
 * @psalm-type AgentJson = array{
 *     object: "agent",
 *     id: string,
 *     agent_type: "notion_ai"|"custom_agent"|"autofill_custom_agent"|"external",
 *     name: string,
 *     description?: string|null,
 *     instructions_page_id?: string|null,
 *     icon?: array{ type: string, ... }|null,
 *     model?: AgentModelJson,
 *     connections?: list<AgentConnectionJson>,
 *     status: "active"|"disabled"|"deleted",
 *     pause_reason?: string|null,
 *     created_by?: AgentCreatorJson|null,
 *     version?: AgentVersionJson|null,
 *     agent_version?: AgentVersionJson|null,
 *     has_unpublished_changes?: bool|"hidden",
 *     last_run_time?: string|null,
 *     last_run_at?: string|null,
 *     credit_limit?: int|"hidden"|null,
 *     triggers?: list<AgentTriggerJson>,
 *     is_favorited?: bool|null,
 *     created_time?: string,
 *     last_edited_time?: string,
 *     instructions?: string|null,
 * }
 *
 * @psalm-immutable
 */
final readonly class Agent
{
    /** ID used to reference the personal agent (Notion AI) */
    public const PERSONAL_AGENT_ID = "notion_ai";

    /**
     * @param list<AgentConnection> $connections
     * @param list<AgentTrigger> $triggers
     */
    private function __construct(
        public string $id,
        public AgentType $type,
        public string $name,
        public string|null $description,
        public string|null $instructionsPageId,
        public Icon|AgentAvatar|null $icon,
        public AgentModel|null $model,
        public array $connections,
        public AgentStatus $status,
        public PauseReason|null $pauseReason,
        public AgentCreator|null $createdBy,
        public AgentVersion|null $version,
        public bool|Hidden|null $hasUnpublishedChanges,
        public DateTimeImmutable|Hidden|null $lastRunTime,
        public int|Hidden|null $creditLimit,
        public array $triggers,
        public bool|null $isFavorited,
        public DateTimeImmutable|null $createdTime,
        public DateTimeImmutable|null $lastEditedTime,
        public string|null $instructions,
    ) {
    }

    /**
     * @param AgentJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        $version = $array["agent_version"] ?? $array["version"] ?? null;

        $hasUnpublishedChanges = $array["has_unpublished_changes"] ?? null;
        if ($hasUnpublishedChanges === "hidden") {
            $hasUnpublishedChanges = Hidden::Hidden;
        }

        $lastRunTime = $array["last_run_at"] ?? $array["last_run_time"] ?? null;
        $lastRunTime = match ($lastRunTime) {
            null => null,
            "hidden" => Hidden::Hidden,
            default => new DateTimeImmutable($lastRunTime),
        };

        $creditLimit = $array["credit_limit"] ?? null;
        if ($creditLimit === "hidden") {
            $creditLimit = Hidden::Hidden;
        }

        $pauseReason = $array["pause_reason"] ?? null;

        return new self(
            $array["id"],
            AgentType::from($array["agent_type"]),
            $array["name"],
            $array["description"] ?? null,
            $array["instructions_page_id"] ?? null,
            self::iconFromArray($array["icon"] ?? null),
            isset($array["model"]) ? AgentModel::fromArray($array["model"]) : null,
            array_map(
                fn(array $c): AgentConnection => AgentConnection::fromArray($c),
                $array["connections"] ?? [],
            ),
            AgentStatus::from($array["status"]),
            $pauseReason !== null ? PauseReason::from($pauseReason) : null,
            isset($array["created_by"]) ? AgentCreator::fromArray($array["created_by"]) : null,
            $version !== null ? AgentVersion::fromArray($version) : null,
            $hasUnpublishedChanges,
            $lastRunTime,
            $creditLimit,
            array_map(
                fn(array $t): AgentTrigger => AgentTrigger::fromArray($t),
                $array["triggers"] ?? [],
            ),
            $array["is_favorited"] ?? null,
            isset($array["created_time"]) ? new DateTimeImmutable($array["created_time"]) : null,
            isset($array["last_edited_time"]) ? new DateTimeImmutable($array["last_edited_time"]) : null,
            $array["instructions"] ?? null,
        );
    }

    /** @param array{ type: string, ... }|null $array */
    private static function iconFromArray(array|null $array): Icon|AgentAvatar|null
    {
        if ($array === null) {
            return null;
        }

        if ($array["type"] === "custom_agent_avatar") {
            /** @psalm-var array{ type: "custom_agent_avatar", custom_agent_avatar: array{ static_url: string, animated_url: string } } $array */
            return AgentAvatar::fromArray($array);
        }

        if ($array["type"] === "emoji") {
            /** @psalm-var EmojiJson $array */
            return Icon::fromEmoji(Emoji::fromArray($array));
        }

        if ($array["type"] === "file" || $array["type"] === "external") {
            /** @psalm-var FileJson $array */
            return Icon::fromFile(File::fromArray($array));
        }

        return null;
    }

    public function isPersonalAgent(): bool
    {
        return $this->type === AgentType::NotionAi;
    }

    public function isActive(): bool
    {
        return $this->status === AgentStatus::Active;
    }

    public function isDisabled(): bool
    {
        return $this->status === AgentStatus::Disabled;
    }

    public function isDeleted(): bool
    {
        return $this->status === AgentStatus::Deleted;
    }

    public function hasCreditLimit(): bool
    {
        return is_int($this->creditLimit);
    }
}
