# Meeting Notes

Meeting notes blocks represent [AI meeting notes](https://www.notion.com/help/ai-meeting-notes)
in the Notion UI. This block cannot be created or updated, only retrieved from the API.

::: info
Before Notion API version `2026-03-11` this block type was named `transcription`.
:::

```php
use Notion\Blocks\MeetingNotes;
use Notion\Blocks\MeetingNotesStatus;

/** @var MeetingNotes $block */
$block->title;             // RichText[] with the meeting name
$block->toString();        // Meeting name as plain text
$block->status;            // MeetingNotesStatus|null, e.g. MeetingNotesStatus::NotesReady
$block->summaryBlockId;    // ID of the block holding the summary (or null)
$block->notesBlockId;      // ID of the block holding the notes (or null)
$block->transcriptBlockId; // ID of the block holding the transcript (or null)

$block->calendarEvent?->startTime; // DateTimeImmutable|null
$block->calendarEvent?->endTime;   // DateTimeImmutable|null
$block->calendarEvent?->attendees; // string[] user IDs

$block->recording?->startTime; // DateTimeImmutable|null
$block->recording?->endTime;   // DateTimeImmutable|null
```

The summary, notes and transcript are separate blocks. Retrieve them with the
blocks client:

```php
if ($block->transcriptBlockId !== null) {
    $transcript = $notion->blocks()->findChildren($block->transcriptBlockId);
}
```
