<?php

namespace Notion\Agents;

/**
 * @psalm-type AgentCreatorJson = array{
 *     object?: "user",
 *     type: "user"|"bot",
 *     id: string,
 * }
 *
 * @psalm-immutable
 */
final readonly class AgentCreator
{
    private function __construct(
        public string $id,
        public AgentCreatorType $type,
    ) {
    }

    /**
     * @param AgentCreatorJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        return new self(
            $array["id"],
            AgentCreatorType::from($array["type"]),
        );
    }
}
