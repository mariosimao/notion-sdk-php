<?php

namespace Notion\Blocks\MeetingNotes\Query;

use DateTimeImmutable;
use InvalidArgumentException;

/**
 * Date range used by `DateFilter::isWithin()` and `DateFilter::isRelativeTo()`
 *
 * @psalm-immutable
 */
final readonly class DateRange
{
    private function __construct(
        private array $value,
    ) {
    }

    /** Predefined relative range, e.g. "this week" or "the past month" */
    public static function relative(RelativeDateRange $range): self
    {
        return new self([
            "type"  => "relative",
            "value" => $range->value,
        ]);
    }

    /** The last `$count` units (e.g. the past 3 days) */
    public static function past(int $count, DateUnit $unit): self
    {
        return self::custom("past", $count, $unit);
    }

    /** The next `$count` units (e.g. the next 2 weeks) */
    public static function next(int $count, DateUnit $unit): self
    {
        return self::custom("future", $count, $unit);
    }

    /** The current unit surrounding today (e.g. this month) */
    public static function surrounding(DateUnit $unit): self
    {
        return new self([
            "type"  => "relative",
            "value" => "surrounding",
            "unit"  => $unit->value,
        ]);
    }

    /** Exact inclusive date range, either bound can be omitted */
    public static function between(DateTimeImmutable|null $start, DateTimeImmutable|null $end): self
    {
        if ($start === null && $end === null) {
            throw new InvalidArgumentException("At least one of start or end date is required.");
        }

        $value = [ "type" => "daterange" ];
        if ($start !== null) {
            $value["start_date"] = $start->format("Y-m-d");
        }
        if ($end !== null) {
            $value["end_date"] = $end->format("Y-m-d");
        }

        return new self([
            "type"  => "exact",
            "value" => $value,
        ]);
    }

    public function toArray(): array
    {
        return $this->value;
    }

    /** @param "past"|"future" $direction */
    private static function custom(string $direction, int $count, DateUnit $unit): self
    {
        if ($count < 1) {
            throw new InvalidArgumentException("Count must be greater than or equal to 1.");
        }

        return new self([
            "type"      => "relative",
            "value"     => "custom",
            "direction" => $direction,
            "unit"      => $unit->value,
            "count"     => $count,
        ]);
    }
}
