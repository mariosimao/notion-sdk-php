<?php

namespace Notion\Agents;

/**
 * @psalm-type AgentModelJson = array{
 *     mode: "auto"|"pinned",
 *     id?: string|null,
 * }
 *
 * @psalm-immutable
 */
final readonly class AgentModel
{
    private function __construct(
        public AgentModelMode $mode,
        public string|null $id,
    ) {
    }

    /**
     * @param AgentModelJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        return new self(
            AgentModelMode::from($array["mode"]),
            $array["id"] ?? null,
        );
    }

    public function isAuto(): bool
    {
        return $this->mode === AgentModelMode::Auto;
    }

    public function isPinned(): bool
    {
        return $this->mode === AgentModelMode::Pinned;
    }
}
