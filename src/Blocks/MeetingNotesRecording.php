<?php

namespace Notion\Blocks;

use DateTimeImmutable;
use Notion\Common\Date;

/**
 * @psalm-type MeetingNotesRecordingJson = array{
 *      start_time: string,
 *      end_time: string,
 * }
 *
 * @psalm-immutable
 */
final readonly class MeetingNotesRecording
{
    private function __construct(
        public DateTimeImmutable $startTime,
        public DateTimeImmutable $endTime,
    ) {
    }

    /**
     * @param MeetingNotesRecordingJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        return new self(
            new DateTimeImmutable($array["start_time"]),
            new DateTimeImmutable($array["end_time"]),
        );
    }

    /** @internal */
    public function toArray(): array
    {
        return [
            "start_time" => $this->startTime->format(Date::FORMAT),
            "end_time"   => $this->endTime->format(Date::FORMAT),
        ];
    }
}
