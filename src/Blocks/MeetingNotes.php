<?php

namespace Notion\Blocks;

use Notion\Blocks\MeetingNotes\CalendarEvent;
use Notion\Blocks\MeetingNotes\Recording;
use Notion\Blocks\MeetingNotes\Status;
use Notion\Blocks\MeetingNotes\Tabs;
use Notion\Common\RichText;
use Notion\Exceptions\BlockException;

/**
 * AI meeting notes block (read-only)
 *
 * @psalm-import-type BlockMetadataJson from BlockMetadata
 * @psalm-import-type RichTextJson from \Notion\Common\RichText
 * @psalm-import-type TabsJson from \Notion\Blocks\MeetingNotes\Tabs
 * @psalm-import-type CalendarEventJson from \Notion\Blocks\MeetingNotes\CalendarEvent
 * @psalm-import-type RecordingJson from \Notion\Blocks\MeetingNotes\Recording
 *
 * @psalm-type MeetingNotesJson = array{
 *      meeting_notes: array{
 *          is_full_page: bool,
 *          title?: list<RichTextJson>,
 *          status?: string,
 *          children?: TabsJson,
 *          calendar_event?: CalendarEventJson,
 *          recording?: RecordingJson,
 *      },
 * }
 *
 * @psalm-immutable
 */
final readonly class MeetingNotes implements BlockInterface
{
    /**
     * @param RichText[] $title
     * @param Status|null $status Processing status, `null` when absent or unknown
     * @param Tabs|null $tabs Block IDs of the summary, notes and transcript tabs
     */
    private function __construct(
        private BlockMetadata $metadata,
        public bool $isFullPage,
        public array $title,
        public Status|null $status,
        public Tabs|null $tabs,
        public CalendarEvent|null $calendarEvent,
        public Recording|null $recording,
    ) {
        $metadata->checkType(BlockType::MeetingNotes);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var BlockMetadataJson $array */
        $metadata = BlockMetadata::fromArray($array);

        /** @psalm-var MeetingNotesJson $array */
        $meetingNotes = $array["meeting_notes"];

        $title = array_map(fn($t) => RichText::fromArray($t), $meetingNotes["title"] ?? []);

        $status = isset($meetingNotes["status"]) ? Status::tryFrom($meetingNotes["status"]) : null;

        $tabs = isset($meetingNotes["children"]) ? Tabs::fromArray($meetingNotes["children"]) : null;

        $calendarEvent = isset($meetingNotes["calendar_event"]) ?
            CalendarEvent::fromArray($meetingNotes["calendar_event"]) : null;

        $recording = isset($meetingNotes["recording"]) ? Recording::fromArray($meetingNotes["recording"]) : null;

        return new self(
            $metadata,
            $meetingNotes["is_full_page"],
            $title,
            $status,
            $tabs,
            $calendarEvent,
            $recording,
        );
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();

        $meetingNotes = [
            "is_full_page" => $this->isFullPage,
            "title"        => array_map(fn(RichText $t) => $t->toArray(), $this->title),
        ];

        if ($this->status !== null) {
            $meetingNotes["status"] = $this->status->value;
        }

        if ($this->tabs !== null) {
            $meetingNotes["children"] = $this->tabs->toArray();
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
            $this->isFullPage,
            $this->title,
            $this->status,
            $this->tabs,
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
