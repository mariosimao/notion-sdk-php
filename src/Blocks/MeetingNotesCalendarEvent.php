<?php

namespace Notion\Blocks;

use DateTimeImmutable;
use Notion\Common\Date;

/**
 * Calendar metadata of a meeting notes block.
 *
 * @psalm-type MeetingNotesCalendarEventJson = array{
 *      start_time?: string,
 *      end_time?: string,
 *      attendees?: list<string>,
 * }
 *
 * @psalm-immutable
 */
final readonly class MeetingNotesCalendarEvent
{
    /**
     * @param list<string> $attendees IDs of the users attending the meeting
     */
    private function __construct(
        public DateTimeImmutable|null $startTime,
        public DateTimeImmutable|null $endTime,
        public array $attendees,
    ) {
    }

    /**
     * @psalm-param MeetingNotesCalendarEventJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        return new self(
            isset($array["start_time"]) ? new DateTimeImmutable($array["start_time"]) : null,
            isset($array["end_time"]) ? new DateTimeImmutable($array["end_time"]) : null,
            $array["attendees"] ?? [],
        );
    }

    /**
     * @psalm-return MeetingNotesCalendarEventJson
     *
     * @internal
     */
    public function toArray(): array
    {
        $array = [];

        if ($this->startTime !== null) {
            $array["start_time"] = $this->startTime->format(Date::FORMAT);
        }

        if ($this->endTime !== null) {
            $array["end_time"] = $this->endTime->format(Date::FORMAT);
        }

        if ($this->attendees !== []) {
            $array["attendees"] = $this->attendees;
        }

        return $array;
    }
}
