<?php

namespace Notion\Blocks;

/**
 * IDs of the child blocks holding the meeting notes content.
 *
 * @psalm-type MeetingNotesChildrenJson = array{
 *      summary_block_id?: string|null,
 *      notes_block_id?: string|null,
 *      transcript_block_id?: string|null,
 * }
 *
 * @psalm-immutable
 */
final readonly class MeetingNotesChildren
{
    private function __construct(
        public string|null $summaryBlockId,
        public string|null $notesBlockId,
        public string|null $transcriptBlockId,
    ) {
    }

    /**
     * @param MeetingNotesChildrenJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        return new self(
            $array["summary_block_id"] ?? null,
            $array["notes_block_id"] ?? null,
            $array["transcript_block_id"] ?? null,
        );
    }

    /** @internal */
    public function toArray(): array
    {
        return array_filter([
            "summary_block_id"    => $this->summaryBlockId,
            "notes_block_id"      => $this->notesBlockId,
            "transcript_block_id" => $this->transcriptBlockId,
        ], fn($id) => $id !== null);
    }
}
