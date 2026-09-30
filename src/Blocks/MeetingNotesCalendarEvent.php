<?php

namespace Notion\Blocks;

use DateTimeImmutable;
use Notion\Common\Date;

/**
 * @psalm-type MeetingNotesCalendarEventJson = array{
 *      start_time: string,
 *      end_time: string,
 *      attendees?: list<string>,
 * }
 *
 * @psalm-immutable
 */
final readonly class MeetingNotesCalendarEvent
{
    /** @param list<string> $attendees User IDs */
    private function __construct(
        public DateTimeImmutable $startTime,
        public DateTimeImmutable $endTime,
        public array $attendees,
    ) {
    }

    /**
     * @param MeetingNotesCalendarEventJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        return new self(
            new DateTimeImmutable($array["start_time"]),
            new DateTimeImmutable($array["end_time"]),
            $array["attendees"] ?? [],
        );
    }

    /** @internal */
    public function toArray(): array
    {
        return [
            "attendees"  => $this->attendees,
            "start_time" => $this->startTime->format(Date::FORMAT),
            "end_time"   => $this->endTime->format(Date::FORMAT),
        ];
    }
}
