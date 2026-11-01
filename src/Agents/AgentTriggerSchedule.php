<?php

namespace Notion\Agents;

use DateTimeImmutable;

/**
 * Recurrence cadence of a recurrence trigger
 *
 * @psalm-type AgentTriggerScheduleJson = array{
 *     frequency: string,
 *     interval: int,
 *     weekdays?: list<string>,
 *     monthdays?: list<int>,
 *     week_numbers?: list<int>,
 *     hour?: int,
 *     minute?: int,
 *     timezone?: string,
 *     start_date?: string,
 *     end?: array{ type: "date", end_at: string }|array{ type: "count", occurrences: int },
 * }
 *
 * @psalm-immutable
 */
final readonly class AgentTriggerSchedule
{
    /**
     * @param list<string> $weekdays
     * @param list<int> $monthdays
     * @param list<int> $weekNumbers
     */
    private function __construct(
        public string $frequency,
        public int $interval,
        public array $weekdays,
        public array $monthdays,
        public array $weekNumbers,
        public int|null $hour,
        public int|null $minute,
        public string|null $timezone,
        public DateTimeImmutable|null $startDate,
        public DateTimeImmutable|null $endAt,
        public int|null $endOccurrences,
    ) {
    }

    /**
     * @param AgentTriggerScheduleJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        $end = $array["end"] ?? null;
        $endAt = null;
        $endOccurrences = null;
        if (isset($end["end_at"])) {
            $endAt = new DateTimeImmutable($end["end_at"]);
        }
        if (isset($end["occurrences"])) {
            $endOccurrences = $end["occurrences"];
        }

        return new self(
            $array["frequency"],
            $array["interval"],
            $array["weekdays"] ?? [],
            $array["monthdays"] ?? [],
            $array["week_numbers"] ?? [],
            $array["hour"] ?? null,
            $array["minute"] ?? null,
            $array["timezone"] ?? null,
            isset($array["start_date"]) ? new DateTimeImmutable($array["start_date"]) : null,
            $endAt,
            $endOccurrences,
        );
    }
}
