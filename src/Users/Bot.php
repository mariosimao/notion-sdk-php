<?php

namespace Notion\Users;

/**
 * @psalm-import-type WorkspaceLimitsJson from WorkspaceLimits
 *
 * @psalm-type BotJson = array{
 *    object: "bot",
 *    workspace_limits: WorkspaceLimitsJson,
 * }
 *
 * @psalm-immutable
 */
class Bot
{
    private function __construct(
        public readonly WorkspaceLimits $workspaceLimits
    ) {
    }

    /**
     * @param BotJson $array
     *
     * @psalm-suppress PossiblyUnusedParam
     */
    public static function fromArray(array $array): self
    {
        $workspaceLimits = WorkspaceLimits::fromArray($array["workspace_limits"]);

        return new self($workspaceLimits);
    }

    /** @return BotJson */
    public function toArray(): array
    {
        return [
            "object" => "bot",
            "workspace_limits" => $this->workspaceLimits->toArray(),
        ];
    }
}
