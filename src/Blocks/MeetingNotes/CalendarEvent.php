<?php

namespace Notion\Blocks\MeetingNotes;

use DateTimeImmutable;
use Notion\Common\Date;

/**
 * Calendar event associated with a meeting note
 *
 * @psalm-type CalendarEventJson = array{
 *      start_time: string,
 *      end_time: string,
 *      event_id?: string,
 *      attendees?: list<string>,
 * }
 *
 * @psalm-immutable
 */
final readonly class CalendarEvent
{
    /** @param string[] $attendees User IDs of the attendees */
    private function __construct(
        public DateTimeImmutable $startTime,
        public DateTimeImmutable $endTime,
        public string|null $eventId,
        public array $attendees,
    ) {
    }

    /**
     * @psalm-param CalendarEventJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        return new self(
            new DateTimeImmutable($array["start_time"]),
            new DateTimeImmutable($array["end_time"]),
            $array["event_id"] ?? null,
            $array["attendees"] ?? [],
        );
    }

    /** @internal */
    public function toArray(): array
    {
        $array = [
            "start_time" => $this->startTime->format(Date::FORMAT),
            "end_time"   => $this->endTime->format(Date::FORMAT),
            "attendees"  => $this->attendees,
        ];

        if ($this->eventId !== null) {
            $array["event_id"] = $this->eventId;
        }

        return $array;
    }
}
