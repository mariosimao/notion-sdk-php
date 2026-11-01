<?php

namespace Notion\Agents;

/**
 * Resource an agent connection can access and the scopes granted on it
 *
 * @psalm-type AgentConnectionPermissionJson = array{
 *     target: array{ type: string, ... },
 *     scopes: list<string>,
 * }
 *
 * @psalm-immutable
 */
final readonly class AgentConnectionPermission
{
    /**
     * @param array<array-key, mixed> $target Raw target data, e.g. `["type" => "page", "id" => "..."]`
     * @param list<string> $scopes
     */
    private function __construct(
        public string $targetType,
        public array $target,
        public array $scopes,
    ) {
    }

    /**
     * @param AgentConnectionPermissionJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        return new self(
            $array["target"]["type"],
            $array["target"],
            $array["scopes"],
        );
    }
}
