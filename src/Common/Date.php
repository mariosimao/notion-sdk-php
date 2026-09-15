<?php

namespace Notion\Common;

use DateTimeImmutable;
use DateTimeZone;
use Notion\Exceptions\DateException;

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
        DateTimeZone|string|null $timeZone = null,
    ): self {
        $tz = self::parseTimeZone($timeZone);

        return new self(
            $tz !== null ? $date->setTimezone($tz) : $date,
            null,
            $tz,
        );
    }

    public static function createRange(
        DateTimeImmutable $start,
        DateTimeImmutable $end,
        DateTimeZone|string|null $timeZone = null,
    ): self {
        $tz = self::parseTimeZone($timeZone);

        return new self(
            $tz !== null ? $start->setTimezone($tz) : $start,
            $tz !== null ? $end->setTimezone($tz) : $end,
            $tz,
        );
    }

    public static function now(DateTimeZone|string|null $timeZone = null): self
    {
        $tz = self::parseTimeZone($timeZone);

        return self::create(new DateTimeImmutable("now", $tz), $tz);
    }

    /**
     * @param DateJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        $timeZoneRaw = $array["time_zone"] ?? null;
        $timeZone = self::parseTimeZone($timeZoneRaw);

        if ($timeZone !== null) {
            self::validateDateStringForNamedTimeZone($array["start"]);
            if (isset($array["end"])) {
                self::validateDateStringForNamedTimeZone($array["end"]);
            }
        }

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

    public function changeTimeZone(DateTimeZone|string|null $timeZone): self
    {
        $tz = self::parseTimeZone($timeZone);

        return new self(
            $tz !== null ? $this->start->setTimezone($tz) : $this->start,
            $tz !== null ? $this->end?->setTimezone($tz) : $this->end,
            $tz,
        );
    }

    public function removeTimeZone(): self
    {
        return new self($this->start, $this->end, null);
    }

    /**
     * @psalm-pure
     */
    private static function parseTimeZone(DateTimeZone|string|null $timeZone): DateTimeZone|null
    {
        if ($timeZone === null) {
            return null;
        }

        $name = $timeZone instanceof DateTimeZone ? $timeZone->getName() : $timeZone;

        if ($name === "" || !in_array($name, timezone_identifiers_list(DateTimeZone::ALL_WITH_BC), true)) {
            throw DateException::invalidTimeZone($name);
        }

        /** @psalm-var non-empty-string $name */
        return $timeZone instanceof DateTimeZone ? $timeZone : new DateTimeZone($name);
    }

    /**
     * @psalm-pure
     */
    private static function validateDateStringForNamedTimeZone(string $dateString): void
    {
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateString) === 1) {
            throw DateException::timeInformationRequired();
        }

        if (preg_match('/(Z|[+-]\d{2}(?::?\d{2})?)$/i', $dateString) === 1) {
            throw DateException::utcOffsetNotAllowed();
        }
    }
}
