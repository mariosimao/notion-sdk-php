<?php

namespace Notion\Blocks;

use Notion\Common\RichText;
use Notion\Exceptions\BlockException;

/**
 * Meeting notes block (AI meeting notes).
 *
 * This block cannot be created or updated, only retrieved by the API. Its
 * summary, notes and transcript live in separate blocks, referenced by
 * `$summaryBlockId`, `$notesBlockId` and `$transcriptBlockId`.
 *
 * Named `transcription` before Notion API version 2026-03-11.
 *
 * @psalm-import-type BlockMetadataJson from BlockMetadata
 * @psalm-import-type RichTextJson from \Notion\Common\RichText
 * @psalm-import-type MeetingNotesCalendarEventJson from MeetingNotesCalendarEvent
 * @psalm-import-type MeetingNotesRecordingJson from MeetingNotesRecording
 *
 * @psalm-type MeetingNotesJson = array{
 *      meeting_notes: array{
 *          title?: list<RichTextJson>,
 *          status?: string,
 *          children?: array{
 *              summary_block_id?: string,
 *              notes_block_id?: string,
 *              transcript_block_id?: string,
 *          },
 *          calendar_event?: MeetingNotesCalendarEventJson,
 *          recording?: MeetingNotesRecordingJson,
 *      },
 * }
 *
 * @psalm-immutable
 */
final readonly class MeetingNotes implements BlockInterface
{
    /**
     * @param RichText[] $title
     */
    private function __construct(
        private BlockMetadata $metadata,
        public array $title,
        public MeetingNotesStatus|null $status,
        public string|null $summaryBlockId,
        public string|null $notesBlockId,
        public string|null $transcriptBlockId,
        public MeetingNotesCalendarEvent|null $calendarEvent,
        public MeetingNotesRecording|null $recording,
    ) {
        $metadata->checkType(BlockType::MeetingNotes);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var BlockMetadataJson $array */
        $block = BlockMetadata::fromArray($array);

        /** @psalm-var MeetingNotesJson $array */
        $meetingNotes = $array["meeting_notes"];

        $title = array_map(fn($t) => RichText::fromArray($t), $meetingNotes["title"] ?? []);
        $status = isset($meetingNotes["status"]) ? MeetingNotesStatus::tryFrom($meetingNotes["status"]) : null;
        $children = $meetingNotes["children"] ?? [];

        return new self(
            $block,
            $title,
            $status,
            $children["summary_block_id"] ?? null,
            $children["notes_block_id"] ?? null,
            $children["transcript_block_id"] ?? null,
            isset($meetingNotes["calendar_event"])
                ? MeetingNotesCalendarEvent::fromArray($meetingNotes["calendar_event"])
                : null,
            isset($meetingNotes["recording"])
                ? MeetingNotesRecording::fromArray($meetingNotes["recording"])
                : null,
        );
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();

        $meetingNotes = [];

        if ($this->title !== []) {
            $meetingNotes["title"] = array_map(fn(RichText $t) => $t->toArray(), $this->title);
        }

        if ($this->status !== null) {
            $meetingNotes["status"] = $this->status->value;
        }

        $children = [];
        if ($this->summaryBlockId !== null) {
            $children["summary_block_id"] = $this->summaryBlockId;
        }
        if ($this->notesBlockId !== null) {
            $children["notes_block_id"] = $this->notesBlockId;
        }
        if ($this->transcriptBlockId !== null) {
            $children["transcript_block_id"] = $this->transcriptBlockId;
        }
        if ($children !== []) {
            $meetingNotes["children"] = $children;
        }

        if ($this->calendarEvent !== null) {
            $meetingNotes["calendar_event"] = $this->calendarEvent->toArray();
        }

        if ($this->recording !== null) {
            $meetingNotes["recording"] = $this->recording->toArray();
        }

        $array["meeting_notes"] = $meetingNotes === [] ? new \stdClass() : $meetingNotes;

        return $array;
    }

    /** Plain text of the meeting notes title */
    public function toString(): string
    {
        $string = "";
        foreach ($this->title as $richText) {
            $string = $string . $richText->plainText;
        }

        return $string;
    }

    public function metadata(): BlockMetadata
    {
        return $this->metadata;
    }

    public function addChild(BlockInterface $child): never
    {
        throw BlockException::noChindrenSupport();
    }

    public function changeChildren(BlockInterface ...$children): never
    {
        throw BlockException::noChindrenSupport();
    }

    public function delete(): BlockInterface
    {
        return new self(
            $this->metadata->delete(),
            $this->title,
            $this->status,
            $this->summaryBlockId,
            $this->notesBlockId,
            $this->transcriptBlockId,
            $this->calendarEvent,
            $this->recording,
        );
    }

    /**
     * @deprecated 1.17.0 Use `delete()` instead.
     * @codeCoverageIgnore
     */
    public function archive(): BlockInterface
    {
        return $this->delete();
    }
}
