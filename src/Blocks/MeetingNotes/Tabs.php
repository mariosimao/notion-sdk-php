<?php

namespace Notion\Blocks\MeetingNotes;

/**
 * Block IDs of each meeting note tab (summary, notes and transcript)
 *
 * @psalm-type TabsJson = array{
 *      summary_block_id?: string,
 *      notes_block_id?: string,
 *      transcript_block_id?: string,
 * }
 *
 * @psalm-immutable
 */
final readonly class Tabs
{
    private function __construct(
        public string|null $summaryBlockId,
        public string|null $notesBlockId,
        public string|null $transcriptBlockId,
    ) {
    }

    /**
     * @psalm-param TabsJson $array
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
        $array = [];

        if ($this->summaryBlockId !== null) {
            $array["summary_block_id"] = $this->summaryBlockId;
        }

        if ($this->notesBlockId !== null) {
            $array["notes_block_id"] = $this->notesBlockId;
        }

        if ($this->transcriptBlockId !== null) {
            $array["transcript_block_id"] = $this->transcriptBlockId;
        }

        return $array;
    }
}
