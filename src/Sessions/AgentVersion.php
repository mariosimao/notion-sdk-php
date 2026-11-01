<?php

namespace Notion\Sessions;

use DateTimeImmutable;
use Notion\Common\Date;

/**
 * @psalm-type AgentVersionJson = array{
 *      id: string,
 *      number: int,
 *      published_at: string,
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
        return new self($array["id"], $array["number"], new DateTimeImmutable($array["published_at"]));
    }

    /** @return AgentVersionJson */
    public function toArray(): array
    {
        return [
            "id" => $this->id,
            "number" => $this->number,
            "published_at" => $this->publishedAt->format(Date::FORMAT),
        ];
    }
}
