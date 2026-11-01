# Agent sessions

Custom agents can be run through the [Agent API](https://developers.notion.com/reference/update-session).
A session is a conversation with an agent. Every message starts a new *turn*
that the agent processes asynchronously.

::: info
The Agent API requires the `2026-03-11` Notion API version. The sessions client
sends it automatically, even when the SDK is configured with an older version.
:::

Session objects have the following fields:

```php
$session->id;               // 2f1c2a3b...
$session->agentId;          // 8a7b6c5d...
$session->title;            // "Weekly report"
$session->status;           // SessionStatus (queued, in_progress, requires_action, completed, failed, ...)
$session->requiredActions;  // RequiredAction array
$session->error;            // SessionError|null
$session->createdAt;        // 2026-08-14
$session->updatedAt;        // 2026-08-14
$session->toolCallCount;    // 3
$session->creditsUsed;      // 1.5
```

## Start a session

```php
use Notion\Sessions\Message;

$session = $notion->sessions()->send(
    Message::start($agentId, "Summarize this week's tasks."),
);
```

Use `Message::NOTION_AI` as agent ID to talk with Notion AI. Files uploaded with
the file uploads API can be attached:

```php
use Notion\Sessions\Attachment;

$message = Message::start($agentId, "Review this file")
    ->addAttachment(Attachment::fileUpload($fileUploadId))
    ->changeMetadata([ "source" => "my-app" ])
    ->changePromptContext("The user is a project manager.");
```

## Reply to a session

```php
$session = $notion->sessions()->send(
    Message::reply($session->id, "Now send it to the team."),
);
```

## Poll a session

```php
$session = $notion->sessions()->find($session->id);

while (!$session->status->isTerminal() && !$session->requiresAction()) {
    sleep(2);
    $session = $notion->sessions()->find($session->id);
}
```

## Stream a turn

`stream()` sends the message (or action submission) and yields the
server-sent events of the turn.

```php
use Notion\Sessions\Events\AgentMessage;
use Notion\Sessions\StreamEventType;

$lastEventId = null;
foreach ($notion->sessions()->stream(Message::start($agentId, "Hello!")) as $event) {
    if ($event->type === StreamEventType::EventCommitted) {
        $lastEventId = $event->event->id;
        if ($event->event instanceof AgentMessage) {
            echo $event->event->toString();
        }
    }
}
```

Stream event types are `session.snapshot`, `event.provisional`,
`event.committed`, `stream.timeout`, `stream.end` and `stream.error`.
`$event->isFinal()` tells when the stream is over.

::: warning
Events are read from the HTTP response body. Some PSR-18 clients buffer the
whole body before returning it, in which case all events are yielded at once
when the turn finishes.
:::

After a `stream.timeout`, resume the stream from the last committed event:

```php
$events = $notion->sessions()->resumeStream($sessionId, $lastEventId);
```

## Submit required actions

When a session is in the `requires_action` status, the agent waits for actions
to be approved or rejected.

```php
use Notion\Sessions\ActionSubmission;

$submission = ActionSubmission::create($session->id);
foreach ($session->requiredActions as $action) {
    $submission = $submission->approve($action);
}

$session = $notion->sessions()->submitActions($submission);
```

## Query sessions

```php
use Notion\Sessions\Query;
use Notion\Sessions\SessionFilter;
use Notion\Sessions\SessionStatus;
use Notion\Sessions\Sort;

$query = Query::create()
    ->changeTitle("report")
    ->changeFilter(SessionFilter::and(
        SessionFilter::agentId($agentId),
        SessionFilter::status(SessionStatus::Completed, SessionStatus::Failed),
    ))
    ->changeSorts(Sort::createdAt()->ascending())
    ->changePageSize(20);

$result = $notion->sessions()->query($query);
$result->sessions;   // Session array
$result->hasMore;
$result->nextCursor;

$all = $notion->sessions()->queryAll($query);
```

## Read session events

```php
use Notion\Sessions\Events\EventFilter;
use Notion\Sessions\Events\EventQuery;
use Notion\Sessions\Events\EventType;
use Notion\Sessions\Events\AgentMessage;

$query = EventQuery::create()
    ->changeFilter(EventFilter::type(EventType::AgentMessage));

foreach ($notion->sessions()->queryAllEvents($sessionId, $query) as $event) {
    if ($event instanceof AgentMessage) {
        echo $event->toString();
    }
}
```

Available event classes are `UserMessage`, `AgentMessage`, `AgentThinking`,
`AgentToolUse`, `AgentToolResult`, `StatusChange` and `UnknownEvent` (for event
types not yet supported by the SDK).

## Cancel a session

```php
$session = $notion->sessions()->cancel($sessionId);

// Cancel only if the given event is still the latest one
$session = $notion->sessions()->cancel($sessionId, $eventId);
```
