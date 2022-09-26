<?php

namespace Notion\Common;

use DateTimeImmutable;

/**
 * @psalm-type DateJson = array{ start: string, end?: string|null }
 *
 * @psalm-immutable
 */
class Date
{
    public const FORMAT = "Y-m-d\TH:i:s.up";

    private function __construct(
        public readonly DateTimeImmutable $start,
        public readonly DateTimeImmutable|null $end,
    ) {
    }

    public static function create(DateTimeImmutable $date): self
    {
        return new self($date, null);
    }

    public static function createRange(
        DateTimeImmutable $start,
        DateTimeImmutable $end,
    ): self {
        return new self($start, $end);
    }

    public static function now(): self
    {
        return self::create(new DateTimeImmutable("now"));
    }

    /**
     * @param DateJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        $start = new DateTimeImmutable($array["start"]);
        $end = isset($array["end"]) ? new DateTimeImmutable($array["end"]) : null;

        return new self($start, $end);
    }

    public function toArray(): array
    {
        return [
            "start" => $this->start->format(self::FORMAT),
            "end"   => $this->end?->format(self::FORMAT),
        ];
    }

    public function isRange(): bool
    {
        return $this->end !== null;
    }

    public function changeStart(DateTimeImmutable $start): self
    {
        return new self($start, $this->end);
    }

    public function changeEnd(DateTimeImmutable $end): self
    {
        return new self($this->start, $end);
    }

    public function removeEnd(): self
    {
        return new self($this->start, null);
    }
}
