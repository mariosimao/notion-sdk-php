<?php

namespace Notion\Common;

use DateTimeImmutable;
use DateTimeZone;
use Notion\Exceptions\DateException;

/**
 * @psalm-type DateJson = array{
 *      start: string,
 *      end?: string|null,
 *      time_zone?: string|null,
 * }
 *
 * @psalm-immutable
 */
final readonly class Date
{
    public const FORMAT = "Y-m-d\TH:i:s.up";
    public const FORMAT_WITHOUT_OFFSET = "Y-m-d\TH:i:s.u";

    /**
     * When `$timeZone` is set, `$start` and `$end` are always expressed in it.
     */
    private function __construct(
        public DateTimeImmutable $start,
        public DateTimeImmutable|null $end,
        public DateTimeZone|null $timeZone,
    ) {
    }

    /**
     * @throws DateException If `$timeZone` is not a named IANA time zone.
     */
    public static function create(
        DateTimeImmutable $date,
        DateTimeZone|null $timeZone = null,
    ): self {
        self::validateTimeZone($timeZone);

        return new self(self::convert($date, $timeZone), null, $timeZone);
    }

    /**
     * @throws DateException If `$timeZone` is not a named IANA time zone.
     */
    public static function createRange(
        DateTimeImmutable $start,
        DateTimeImmutable $end,
        DateTimeZone|null $timeZone = null,
    ): self {
        self::validateTimeZone($timeZone);

        return new self(
            self::convert($start, $timeZone),
            self::convert($end, $timeZone),
            $timeZone,
        );
    }

    /**
     * @throws DateException If `$timeZone` is not a named IANA time zone.
     */
    public static function now(DateTimeZone|null $timeZone = null): self
    {
        return self::create(new DateTimeImmutable("now"), $timeZone);
    }

    /**
     * @param DateJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        $timeZoneName = $array["time_zone"] ?? null;
        $timeZone = $timeZoneName !== null && $timeZoneName !== "" ? new DateTimeZone($timeZoneName) : null;

        // Values without offset are wall-clock times in the named time zone.
        $start = self::convert(new DateTimeImmutable($array["start"], $timeZone), $timeZone);
        $end = isset($array["end"])
            ? self::convert(new DateTimeImmutable($array["end"], $timeZone), $timeZone)
            : null;

        return new self($start, $end, $timeZone);
    }

    public function toArray(): array
    {
        // Notion rejects UTC offsets in start and end when a time zone is given.
        $format = $this->timeZone === null ? self::FORMAT : self::FORMAT_WITHOUT_OFFSET;

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
        return new self(self::convert($start, $this->timeZone), $this->end, $this->timeZone);
    }

    public function changeEnd(DateTimeImmutable $end): self
    {
        return new self($this->start, self::convert($end, $this->timeZone), $this->timeZone);
    }

    public function removeEnd(): self
    {
        return new self($this->start, null, $this->timeZone);
    }

    /**
     * Keeps the same instants, expressing them in the new time zone.
     *
     * @throws DateException If `$timeZone` is not a named IANA time zone.
     */
    public function changeTimeZone(DateTimeZone $timeZone): self
    {
        self::validateTimeZone($timeZone);

        return new self(
            self::convert($this->start, $timeZone),
            $this->end !== null ? self::convert($this->end, $timeZone) : null,
            $timeZone,
        );
    }

    /**
     * Keeps the same instants, which will be sent with their UTC offsets.
     */
    public function removeTimeZone(): self
    {
        return new self($this->start, $this->end, null);
    }

    /** @psalm-mutation-free */
    private static function validateTimeZone(DateTimeZone|null $timeZone): void
    {
        if ($timeZone === null) {
            return;
        }

        $name = $timeZone->getName();
        if (!in_array($name, DateTimeZone::listIdentifiers(DateTimeZone::ALL_WITH_BC), true)) {
            throw DateException::invalidTimeZone($name);
        }
    }

    /** @psalm-mutation-free */
    private static function convert(DateTimeImmutable $date, DateTimeZone|null $timeZone): DateTimeImmutable
    {
        return $timeZone === null ? $date : $date->setTimezone($timeZone);
    }
}
