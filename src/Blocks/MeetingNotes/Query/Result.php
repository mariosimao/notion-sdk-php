<?php

namespace Notion\Blocks\MeetingNotes\Query;

use Notion\Blocks\MeetingNotes;

/**
 * Meeting notes query result
 *
 * @psalm-type QueryResultJson = array{
 *      results: list<array{ type: string, ... }>,
 *      has_more: bool,
 * }
 *
 * @psalm-immutable
 */
final readonly class Result
{
    /** @param MeetingNotes[] $meetingNotes */
    private function __construct(
        public array $meetingNotes,
        public bool $hasMore,
    ) {
    }

    /** @param QueryResultJson $array */
    public static function fromArray(array $array): self
    {
        $meetingNotes = array_map(
            fn (array $blockArray): MeetingNotes => MeetingNotes::fromArray($blockArray),
            $array["results"],
        );

        return new self($meetingNotes, $array["has_more"]);
    }
}
