<?php

namespace Notion\Agents;

use DateTimeImmutable;

/**
 * @psalm-type AgentVersionJson = array{
 *     id: string,
 *     number: int|float,
 *     published_at: string,
 * }
 *
 * @psalm-immutable
 */
final readonly class AgentVersion
{
    private function __construct(
        public string $id,
        public int $number,
        public DateTimeImmutable $publishedAt,
    ) {
    }

    /**
     * @param AgentVersionJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        return new self(
            $array["id"],
            (int) $array["number"],
            new DateTimeImmutable($array["published_at"]),
        );
    }
}
