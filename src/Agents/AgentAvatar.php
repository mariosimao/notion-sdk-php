<?php

namespace Notion\Agents;

/**
 * Custom Agent avatar icon
 *
 * @psalm-type AgentAvatarJson = array{
 *     type: "custom_agent_avatar",
 *     custom_agent_avatar: array{ static_url: string, animated_url: string },
 * }
 *
 * @psalm-immutable
 */
final readonly class AgentAvatar
{
    private function __construct(
        public string $staticUrl,
        public string $animatedUrl,
    ) {
    }

    /**
     * @param AgentAvatarJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        return new self(
            $array["custom_agent_avatar"]["static_url"],
            $array["custom_agent_avatar"]["animated_url"],
        );
    }
}
