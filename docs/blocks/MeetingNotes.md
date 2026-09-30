# Meeting Notes

Meeting notes blocks represent AI meeting notes in the Notion UI. They cannot be
created or updated through the block endpoints, only retrieved from the API.

The block holds meeting metadata and the IDs of the child blocks with the
generated summary, notes and transcript.

```php
use Notion\Blocks\MeetingNotes;
use Notion\Blocks\MeetingNotesStatus;

/** @var MeetingNotes $block */
$block->title;                        // RichText[]
$block->toString();                   // Title as plain text
$block->status;                       // MeetingNotesStatus|null
$block->calendarEvent?->startTime;    // DateTimeImmutable
$block->calendarEvent?->endTime;      // DateTimeImmutable
$block->calendarEvent?->attendees;    // User IDs
$block->recording?->startTime;        // DateTimeImmutable
$block->recording?->endTime;          // DateTimeImmutable

if ($block->status === MeetingNotesStatus::NotesReady) {
    $summaryId = $block->children?->summaryBlockId;
    $notesId = $block->children?->notesBlockId;
    $transcriptId = $block->children?->transcriptBlockId;

    $summary = $notion->blocks()->findChildren($summaryId);
}
```

::: info
API versions prior to `2026-03-11` return this block as `transcription`. The SDK
parses both names into `MeetingNotes`.
:::
