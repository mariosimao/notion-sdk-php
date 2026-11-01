# MeetingNotes

AI meeting notes block. Meeting notes are read-only: they can be retrieved and
queried, but not created or changed.

## Properties

```php
$block->isFullPage;     // bool
$block->title;          // RichText[]
$block->toString();     // Title as plain text
$block->status;         // Notion\Blocks\MeetingNotes\Status|null, e.g. Status::NotesReady
$block->tabs;           // Block IDs of the summary, notes and transcript tabs
$block->calendarEvent;  // Start/end time, event ID and attendee user IDs
$block->recording;      // Recording start/end time
```

Use the tab block IDs to read the meeting content:

```php
$summary = $notion->blocks()->findChildren($block->tabs->summaryBlockId);
```

## Query meeting notes

Returns meeting notes where the user tied to the integration is an attendee.
The integration needs the "Read content" capability, and the workspace must
have AI meeting notes for the integration's user.

```php
use Notion\Blocks\MeetingNotes\Query;
use Notion\Blocks\MeetingNotes\Query\CompoundFilter;
use Notion\Blocks\MeetingNotes\Query\DateFilter;
use Notion\Blocks\MeetingNotes\Query\DateRange;
use Notion\Blocks\MeetingNotes\Query\PeopleFilter;
use Notion\Blocks\MeetingNotes\Query\RelativeDateRange;
use Notion\Blocks\MeetingNotes\Query\Sort;
use Notion\Blocks\MeetingNotes\Query\TextFilter;

$query = Query::create()
    ->changeFilter(CompoundFilter::and(
        TextFilter::title()->contains("standup"),
        PeopleFilter::attendees()->contains(
            "3f2b25f3-0a52-4a90-8f0d-5a3f0e6c1d2e",        // user ID
            "user://9b1b1c3e-0f4b-4d2b-8f2e-3f2b25f30a52", // user URI from user search
            $user,                                         // Notion\Users\User
        ),
        DateFilter::createdTime()->isWithin(DateRange::relative(RelativeDateRange::PastMonth)),
    ))
    ->addSort(Sort::lastEditedTime()->descending())
    ->changeLimit(10); // 1 to 50, defaults to 50

$result = $notion->blocks()->queryMeetingNotes($query);

foreach ($result->meetingNotes as $meetingNotes) {
    echo $meetingNotes->toString();
}

$result->hasMore; // More meeting notes match the query beyond the limit
```

The endpoint has no cursor pagination: refine the filter, sorts and limit
instead. Calling `queryMeetingNotes()` without a query returns the latest 50
meeting notes.

### Filters

| Filter                                     | Operators                                                                                |
|--------------------------------------------|------------------------------------------------------------------------------------------|
| `TextFilter::title()`                      | `equals`, `doesNotEqual`, `contains`, `doesNotContain`, `startsWith`, `endsWith`, `isEmpty`, `isNotEmpty` |
| `PeopleFilter::attendees()`, `createdBy()`, `lastEditedBy()` | `contains`, `doesNotContain`, `containsMe`, `isEmpty`, `isNotEmpty`    |
| `DateFilter::createdTime()`, `lastEditedTime()` | `equals`, `before`, `after`, `onOrBefore`, `onOrAfter`, `isWithin`, `isRelativeTo`, `isEmpty`, `isNotEmpty` |

People filters accept user IDs (plain UUID or `user://<uuid>`), `User` objects
or `UserReference` values; they are normalized to plain user IDs. Use
`UserReference::me()` or `containsMe()` to match the integration's user.

Date filters accept a `DateTimeImmutable` or a
`Notion\DataSources\Query\RelativeDate`. Pass `includeTime: true` to compare the
time of day too:

```php
DateFilter::createdTime()->onOrAfter(RelativeDate::OneWeekAgo);
DateFilter::lastEditedTime()->before(new DateTimeImmutable("2026-01-15 09:30", new DateTimeZone("America/Los_Angeles")), includeTime: true);
DateFilter::createdTime()->isWithin(DateRange::past(3, DateUnit::Day));
DateFilter::createdTime()->isWithin(DateRange::between(new DateTimeImmutable("2026-01-01"), new DateTimeImmutable("2026-01-31")));
```

`CompoundFilter::and()` and `CompoundFilter::or()` support one level of nesting:
a nested compound filter can only contain property filters.
