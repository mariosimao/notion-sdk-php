<?php

namespace Notion\Common;

use DateTimeImmutable;

class Date
{
    private DateTimeImmutable $start;
    private DateTimeImmutable|null $end;

    private function __construct(DateTimeImmutable $start, DateTimeImmutable|null $end)
    {
        $this->start = $start;
        $this->end = $end;
    }

    public static function fromArray(array $array): self
    {
        $start = new DateTimeImmutable($array["start"]);
        $end = isset($array["end"]) ? new DateTimeImmutable($array["end"]) : null;

        return new self($start, $end);
    }

    public function toArray(): array
    {
        return [
            "start" => $this->start->format("Y-m-d"),
            "end"   => $this->end?->format("Y-m-d"),
        ];
    }

    public function start(): DateTimeImmutable
    {
        return $this->start;
    }

    public function end(): DateTimeImmutable|null
    {
        return $this->end;
    }

    public function isRange(): bool
    {
        return $this->end !== null;
    }
}
