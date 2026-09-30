# Webhooks

## Introduction

[Integration webhooks](https://developers.notion.com/reference/webhooks) notify
your application when pages, databases, data sources, comments, file uploads,
or views change. Events only reference the changed entity, so fetch the latest
state with the API when you need the content.

## Verifying the subscription

When a subscription is created, Notion sends a one-time request containing a
`verification_token`. Store it: it is required to validate every event.

```php
$body = file_get_contents("php://input");

/** @var array{verification_token?: string} $payload */
$payload = json_decode($body, true);
$verificationToken = $payload["verification_token"] ?? null;
```

## Receiving events

Always verify the `X-Notion-Signature` header against the **raw** request body
before trusting an event. Re-encoded JSON produces different bytes and fails
verification.

```php
use Notion\Webhooks\Event;
use Notion\Webhooks\Signature;

$body = file_get_contents("php://input");
$signature = $_SERVER["HTTP_X_NOTION_SIGNATURE"] ?? "";

if (!Signature::verify($body, $signature, $verificationToken)) {
    http_response_code(401);
    exit;
}

$event = Event::fromJson($body);
```

Event objects have the following fields:

```php
$event->id;             // 367cba44...
$event->timestamp;      // DateTimeImmutable
$event->type;           // EventType::PageCreated
$event->workspaceId;    // 13950b26...
$event->workspaceName;  // Acme
$event->subscriptionId; // 29d75c0d...
$event->integrationId;  // 0ef2e755...
$event->authors;        // Author[] (person, bot or agent)
$event->accessibleBy;   // Author[] (public integrations only)
$event->attemptNumber;  // 1-8
$event->apiVersion;     // 2025-09-03
$event->entity->id;     // 153104cd...
$event->entity->type;   // EntityType::Page
$event->data;           // EventData or null
```

## Event data

The `data` field depends on the event type:

| Event type | Data class |
| :- | :- |
| `page.created`, `page.moved`, `page.deleted`, `page.undeleted`, `page.locked`, `page.unlocked`, `database.created`, `database.moved`, `database.deleted`, `database.undeleted`, `data_source.created`, `data_source.moved`, `data_source.deleted`, `data_source.undeleted`, `view.deleted` | `ParentData` |
| `page.content_updated`, `database.content_updated`, `data_source.content_updated` | `ContentUpdatedData` |
| `page.properties_updated` | `PagePropertiesUpdatedData` |
| `database.schema_updated`, `data_source.schema_updated` | `SchemaUpdatedData` |
| `page.transcription_block.transcript_deleted` | `TranscriptDeletedData` |
| `comment.created`, `comment.updated`, `comment.deleted` | `CommentData` |
| `view.created` | `ViewCreatedData` |
| `view.updated` | `ViewUpdatedData` |
| `file_upload.upload_failed` | `FileUploadFailedData` |
| `file_upload.created`, `file_upload.completed`, `file_upload.expired` | `null` |

```php
use Notion\Webhooks\Data\ContentUpdatedData;
use Notion\Webhooks\EventType;

if ($event->type === EventType::PageContentUpdated) {
    $page = $notion->pages()->find($event->entity->id);
}

if ($event->data instanceof ContentUpdatedData) {
    foreach ($event->data->updatedBlocks as $block) {
        $block->id;
        $block->type; // EntityType::Block
    }
}
```

## Testing your handler

Use `Signature::sign()` to generate valid signatures for test payloads:

```php
use Notion\Webhooks\Signature;

$body = json_encode($payload);
$signature = Signature::sign($body, "test-verification-token");
```
