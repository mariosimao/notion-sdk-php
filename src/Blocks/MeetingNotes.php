<?php

namespace Notion\Blocks;

use Notion\Common\RichText;
use Notion\Exceptions\BlockException;

/**
 * AI meeting notes block.
 *
 * This block cannot be created, only retrieved by the API. Its content lives in
 * the child blocks referenced by `$children`.
 *
 * @psalm-import-type BlockMetadataJson from BlockMetadata
 * @psalm-import-type RichTextJson from RichText
 * @psalm-import-type MeetingNotesChildrenJson from MeetingNotesChildren
 * @psalm-import-type MeetingNotesCalendarEventJson from MeetingNotesCalendarEvent
 * @psalm-import-type MeetingNotesRecordingJson from MeetingNotesRecording
 *
 * @psalm-type MeetingNotesJson = array{
 *      meeting_notes: array{
 *          title?: list<RichTextJson>,
 *          status?: string,
 *          children?: MeetingNotesChildrenJson,
 *          calendar_event?: MeetingNotesCalendarEventJson,
 *          recording?: MeetingNotesRecordingJson,
 *      },
 * }
 *
 * @psalm-immutable
 */
final readonly class MeetingNotes implements BlockInterface
{
    /** Block type name used by API versions prior to `2026-03-11`. */
    public const LEGACY_TYPE = "transcription";

    /** @param RichText[] $title */
    private function __construct(
        private BlockMetadata $metadata,
        public array $title,
        public MeetingNotesStatus|null $status,
        public MeetingNotesChildren|null $children,
        public MeetingNotesCalendarEvent|null $calendarEvent,
        public MeetingNotesRecording|null $recording,
    ) {
        $metadata->checkType(BlockType::MeetingNotes);
    }

    public static function fromArray(array $array): self
    {
        if (($array["type"] ?? null) === self::LEGACY_TYPE) {
            $array["type"] = BlockType::MeetingNotes->value;
            $array[BlockType::MeetingNotes->value] = (array) ($array[self::LEGACY_TYPE] ?? []);
            unset($array[self::LEGACY_TYPE]);
        }

        /** @psalm-var BlockMetadataJson $array */
        $block = BlockMetadata::fromArray($array);

        /** @psalm-var MeetingNotesJson $array */
        $meetingNotes = $array["meeting_notes"];

        $title = array_map(fn($t) => RichText::fromArray($t), $meetingNotes["title"] ?? []);
        $status = isset($meetingNotes["status"]) ? MeetingNotesStatus::tryFrom($meetingNotes["status"]) : null;
        $children = isset($meetingNotes["children"]) ?
            MeetingNotesChildren::fromArray($meetingNotes["children"]) : null;
        $calendarEvent = isset($meetingNotes["calendar_event"]) ?
            MeetingNotesCalendarEvent::fromArray($meetingNotes["calendar_event"]) : null;
        $recording = isset($meetingNotes["recording"]) ?
            MeetingNotesRecording::fromArray($meetingNotes["recording"]) : null;

        return new self($block, $title, $status, $children, $calendarEvent, $recording);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();

        $meetingNotes = [
            "title" => array_map(fn(RichText $t) => $t->toArray(), $this->title),
        ];
        if ($this->status !== null) {
            $meetingNotes["status"] = $this->status->value;
        }
        if ($this->children !== null) {
            $meetingNotes["children"] = $this->children->toArray();
        }
        if ($this->calendarEvent !== null) {
            $meetingNotes["calendar_event"] = $this->calendarEvent->toArray();
        }
        if ($this->recording !== null) {
            $meetingNotes["recording"] = $this->recording->toArray();
        }

        $array["meeting_notes"] = $meetingNotes;

        return $array;
    }

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
            $this->children,
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
