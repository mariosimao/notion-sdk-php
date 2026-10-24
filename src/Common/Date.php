<?php

namespace Notion\Common;

use DateTimeImmutable;
use DateTimeZone;

/**
 * @psalm-type DateJson = array{
 *     start: string,
 *     end?: string|null,
 *     time_zone?: string|null,
 * }
 *
 * @psalm-immutable
 */
final readonly class Date
{
    public const FORMAT = "Y-m-d\TH:i:s.up";
    public const FORMAT_WITHOUT_TIMEZONE = "Y-m-d\TH:i:s.u";
    public const FORMAT_NO_OFFSET = self::FORMAT_WITHOUT_TIMEZONE;

    private function __construct(
        public DateTimeImmutable $start,
        public DateTimeImmutable|null $end,
        public DateTimeZone|null $timeZone = null,
    ) {
    }

    public static function create(
        DateTimeImmutable $date,
        DateTimeZone|null $timeZone = null,
    ): self {
        return new self(
            $timeZone !== null ? $date->setTimezone($timeZone) : $date,
            null,
            $timeZone,
        );
    }

    public static function createRange(
        DateTimeImmutable $start,
        DateTimeImmutable $end,
        DateTimeZone|null $timeZone = null,
    ): self {
        return new self(
            $timeZone !== null ? $start->setTimezone($timeZone) : $start,
            $timeZone !== null ? $end->setTimezone($timeZone) : $end,
            $timeZone,
        );
    }

    public static function now(DateTimeZone|null $timeZone = null): self
    {
        return self::create(new DateTimeImmutable("now", $timeZone), $timeZone);
    }

    /**
     * @param DateJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        /** @psalm-var non-empty-string|null $name */
        $name = $array["time_zone"] ?? null;
        $timeZone = $name !== null ? new DateTimeZone($name) : null;

        $start = new DateTimeImmutable($array["start"], $timeZone);
        $end = isset($array["end"])
            ? new DateTimeImmutable($array["end"], $timeZone)
            : null;

        return new self($start, $end, $timeZone);
    }

    public function toArray(): array
    {
        $format = $this->timeZone !== null ? self::FORMAT_WITHOUT_TIMEZONE : self::FORMAT;

        $array = [
            "start" => $this->start->format($format),
            "end"   => $this->end?->format($format),
        ];

        if ($this->timeZone !== null) {
            $array["time_zone"] = $this->timeZone->getName();
        }

        return $array;
    }

    public function isRange(): bool
    {
        return $this->end !== null;
    }

    public function hasTimeZone(): bool
    {
        return $this->timeZone !== null;
    }

    public function changeStart(DateTimeImmutable $start): self
    {
        return new self(
            $this->timeZone !== null ? $start->setTimezone($this->timeZone) : $start,
            $this->end,
            $this->timeZone,
        );
    }

    public function changeEnd(DateTimeImmutable $end): self
    {
        return new self(
            $this->start,
            $this->timeZone !== null ? $end->setTimezone($this->timeZone) : $end,
            $this->timeZone,
        );
    }

    public function removeEnd(): self
    {
        return new self($this->start, null, $this->timeZone);
    }

    public function changeTimeZone(DateTimeZone|null $timeZone): self
    {
        return new self(
            $timeZone !== null ? $this->start->setTimezone($timeZone) : $this->start,
            $timeZone !== null ? $this->end?->setTimezone($timeZone) : $this->end,
            $timeZone,
        );
    }

    public function removeTimeZone(): self
    {
        return new self($this->start, $this->end, null);
    }
}
