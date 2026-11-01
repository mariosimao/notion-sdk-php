<?php

namespace Notion\Agents\Query;

use Notion\Agents\Agent;

/**
 * Agents query result
 *
 * @psalm-import-type AgentJson from \Notion\Agents\Agent
 *
 * @psalm-type AgentQueryResultJson = array{
 *      results: list<AgentJson>,
 *      has_more: bool,
 *      next_cursor: string|null,
 * }
 *
 * @psalm-immutable
 */
final readonly class Result
{
    /** @param list<Agent> $agents */
    private function __construct(
        public array $agents,
        public bool $hasMore,
        public string|null $nextCursor,
    ) {
    }

    /**
     * @param AgentQueryResultJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        $agents = array_map(
            fn(array $agent): Agent => Agent::fromArray($agent),
            $array["results"],
        );

        return new self($agents, $array["has_more"], $array["next_cursor"]);
    }
}
