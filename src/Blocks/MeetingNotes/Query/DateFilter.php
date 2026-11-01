<?php

namespace Notion\Blocks\MeetingNotes\Query;

use DateTimeImmutable;
use DateTimeZone;
use InvalidArgumentException;
use Notion\DataSources\Query\RelativeDate;

/**
 * Filter meeting notes by creation or last edition time
 *
 * @psalm-immutable
 */
final readonly class DateFilter implements PropertyFilter
{
    private const VALID_PROPERTIES = [
        Property::CreatedTime,
        Property::LastEditedTime,
    ];

    private const POINT_OPERATORS = [
        Operator::DateIs,
        Operator::DateIsBefore,
        Operator::DateIsAfter,
        Operator::DateIsOnOrBefore,
        Operator::DateIsOnOrAfter,
    ];

    private const RANGE_OPERATORS = [
        Operator::DateIsWithin,
        Operator::DateIsRelativeTo,
    ];

    private const EMPTY_OPERATORS = [
        Operator::IsEmpty,
        Operator::IsNotEmpty,
    ];

    private function __construct(
        private Property $property,
        private Operator $operator,
        private array|null $value,
        private bool $useEnd,
    ) {
        if (!in_array($property, self::VALID_PROPERTIES, true)) {
            throw new InvalidArgumentException("Invalid property");
        }

        $validOperators = [...self::POINT_OPERATORS, ...self::RANGE_OPERATORS, ...self::EMPTY_OPERATORS];
        if (!in_array($operator, $validOperators, true)) {
            throw new InvalidArgumentException("Invalid operator");
        }
    }

    public static function createdTime(): self
    {
        return new self(Property::CreatedTime, Operator::IsNotEmpty, null, false);
    }

    public static function lastEditedTime(): self
    {
        return new self(Property::LastEditedTime, Operator::IsNotEmpty, null, false);
    }

    public function property(): Property
    {
        return $this->property;
    }

    public function operator(): Operator
    {
        return $this->operator;
    }

    /**
     * Compare only the calendar date by default. Set `$includeTime` to also
     * compare the time of day, using the date time zone (non IANA time zones
     * are converted to UTC).
     */
    public function equals(DateTimeImmutable|RelativeDate $date, bool $includeTime = false): self
    {
        return $this->point(Operator::DateIs, $date, $includeTime);
    }

    public function before(DateTimeImmutable|RelativeDate $date, bool $includeTime = false): self
    {
        return $this->point(Operator::DateIsBefore, $date, $includeTime);
    }

    public function after(DateTimeImmutable|RelativeDate $date, bool $includeTime = false): self
    {
        return $this->point(Operator::DateIsAfter, $date, $includeTime);
    }

    public function onOrBefore(DateTimeImmutable|RelativeDate $date, bool $includeTime = false): self
    {
        return $this->point(Operator::DateIsOnOrBefore, $date, $includeTime);
    }

    public function onOrAfter(DateTimeImmutable|RelativeDate $date, bool $includeTime = false): self
    {
        return $this->point(Operator::DateIsOnOrAfter, $date, $includeTime);
    }

    public function isWithin(DateRange $range): self
    {
        return new self($this->property, Operator::DateIsWithin, $range->toArray(), $this->useEnd);
    }

    public function isRelativeTo(DateRange $range): self
    {
        return new self($this->property, Operator::DateIsRelativeTo, $range->toArray(), $this->useEnd);
    }

    public function isEmpty(): self
    {
        return new self($this->property, Operator::IsEmpty, null, false);
    }

    public function isNotEmpty(): self
    {
        return new self($this->property, Operator::IsNotEmpty, null, false);
    }

    /** Compare against the end of a date range rather than its start */
    public function useEnd(bool $useEnd = true): self
    {
        return new self($this->property, $this->operator, $this->value, $useEnd);
    }

    public function toArray(): array
    {
        $filter = [ "operator" => $this->operator->value ];

        if (!in_array($this->operator, self::EMPTY_OPERATORS, true)) {
            $filter["value"] = $this->value;

            if ($this->useEnd) {
                $filter["use_end"] = true;
            }
        }

        return [
            "property" => $this->property->value,
            "filter"   => $filter,
        ];
    }

    private function point(Operator $operator, DateTimeImmutable|RelativeDate $date, bool $includeTime): self
    {
        if ($date instanceof RelativeDate) {
            $value = [
                "type"  => "relative",
                "value" => $date->value,
            ];

            return new self($this->property, $operator, $value, $this->useEnd);
        }

        if (!$includeTime) {
            $value = [
                "type"  => "exact",
                "value" => [
                    "type"       => "date",
                    "start_date" => $date->format("Y-m-d"),
                ],
            ];

            return new self($this->property, $operator, $value, $this->useEnd);
        }

        // Offsets (e.g. "+02:00") and abbreviations are not IANA time zones
        if ($date->getTimezone()->getLocation() === false) {
            $date = $date->setTimezone(new DateTimeZone("UTC"));
        }

        $value = [
            "type"  => "exact",
            "value" => [
                "type"       => "datetime",
                "start_date" => $date->format("Y-m-d"),
                "start_time" => $date->format("H:i"),
                "time_zone"  => $date->getTimezone()->getName(),
            ],
        ];

        return new self($this->property, $operator, $value, $this->useEnd);
    }
}
