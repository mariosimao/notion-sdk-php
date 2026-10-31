<?php

namespace Notion\Blocks;

use DateTimeImmutable;
use Notion\Common\Date;

/**
 * Recording time window of a meeting notes block.
 *
 * @psalm-type MeetingNotesRecordingJson = array{
 *      start_time?: string,
 *      end_time?: string,
 * }
 *
 * @psalm-immutable
 */
final readonly class MeetingNotesRecording
{
    private function __construct(
        public DateTimeImmutable|null $startTime,
        public DateTimeImmutable|null $endTime,
    ) {
    }

    /**
     * @psalm-param MeetingNotesRecordingJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        return new self(
            isset($array["start_time"]) ? new DateTimeImmutable($array["start_time"]) : null,
            isset($array["end_time"]) ? new DateTimeImmutable($array["end_time"]) : null,
        );
    }

    /**
     * @psalm-return MeetingNotesRecordingJson
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

        return $array;
    }
}
