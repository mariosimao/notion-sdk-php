# Create meeting notes

Create [AI meeting notes](https://developers.notion.com/reference/create-meeting-note)
from an uploaded audio or video file.

```php
<?php

use Notion\Blocks\MeetingNotesLanguage;
use Notion\Blocks\MeetingNotesRequest;
use Notion\Notion;

$token = $_ENV["NOTION_SECRET"];
$notion = Notion::create($token);

$pageId = "c986d7b0-7051-4f18-b165-cc0b9503ffc2";

// The file upload must have the "uploaded" status
$fileUpload = $notion->fileUploads()->upload("weekly-sync.m4a");

$request = MeetingNotesRequest::fromFileUpload($fileUpload->id, $pageId)
    ->changeTitle("Weekly sync")
    ->changeLanguage(MeetingNotesLanguage::English);

$created = $notion->blocks()->createMeetingNotes($request);

$created->id;    // ID of the meeting notes block
$created->block; // Full block, or null without the "Read content" capability
```

## Create from an existing block

Meeting notes can also be created next to an existing audio, video or file
block backed by a Notion-hosted upload. No parent is needed.

```php
$request = MeetingNotesRequest::fromBlock($audioBlockId)
    ->disableSummary(); // Only transcribe, do not generate a summary

$created = $notion->blocks()->createMeetingNotes($request);
```

## Processing and errors

Processing continues asynchronously. Use `$notion->blocks()->find($created->id)`
to check the processing status.

::: warning
The endpoint is not idempotent: retrying a request may create duplicate meeting
notes blocks.
:::

AI meeting notes are a beta feature. If it is not available to the integration,
an `Notion\Exceptions\InvalidBetaException` is thrown. Other failures (invalid
source, parent or options, or the user lacking access to AI meeting notes)
throw a `Notion\Exceptions\ApiException` with the Notion error code in
`$e->notionCode`.

```php
use Notion\Exceptions\ApiException;
use Notion\Exceptions\InvalidBetaException;

try {
    $notion->blocks()->createMeetingNotes($request);
} catch (InvalidBetaException $e) {
    // Meeting notes are not enabled for this integration
} catch (ApiException $e) {
    // $e->notionCode, e.g. "validation_error"
}
```
