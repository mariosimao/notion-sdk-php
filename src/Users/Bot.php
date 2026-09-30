<?php

namespace Notion\Users;

/**
 * @psalm-import-type WorkspaceLimitsJson from WorkspaceLimits
 *
 * @psalm-type BotJson = array{
 *    object: "bot",
 *    workspace_limits: WorkspaceLimitsJson,
 *    workspace_name?: string|null,
 * }
 *
 * @psalm-immutable
 */
final readonly class Bot
{
    private function __construct(
        public WorkspaceLimits $workspaceLimits,
        public string|null $workspaceName,
    ) {
    }

    /**
     * @param BotJson $array
     *
     * @psalm-suppress PossiblyUnusedParam
     */
    public static function fromArray(array $array): self
    {
        $workspaceLimits = WorkspaceLimits::fromArray($array["workspace_limits"] ?? []);

        return new self($workspaceLimits, $array["workspace_name"] ?? null);
    }

    /** @return BotJson */
    public function toArray(): array
    {
        return [
            "object" => "bot",
            "workspace_limits" => $this->workspaceLimits->toArray(),
            "workspace_name" => $this->workspaceName,
        ];
    }
}
