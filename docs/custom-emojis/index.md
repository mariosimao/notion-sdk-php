# Custom emojis

## Introduction

Lists the custom emojis available in the workspace. Results are cursor
paginated and can be filtered by an exact name match, which is useful for
resolving a custom emoji name to its ID.

## Listing all custom emojis

```php
$emojis = $notion->customEmojis()->findAll(); // Follows pagination automatically

foreach ($emojis as $emoji) {
    $emoji->id;   // string
    $emoji->name; // string
    $emoji->url;  // string
}
```

## Finding a custom emoji by name

```php
$emoji = $notion->customEmojis()->findByName("party-parrot"); // CustomEmoji|null
```

## Paginating manually

```php
use Notion\CustomEmojis\Query;

$query = Query::all()                  // All custom emojis...
    ->changeName("party-parrot")       // ... or filter by exact name
    ->changePageSize(50)               // 1 to 100
    ->changeStartCursor($nextCursor);  // Cursor from a previous result

$result = $notion->customEmojis()->list($query);

$result->results;    // array of CustomEmoji objects
$result->hasMore;    // bool
$result->nextCursor; // string|null
```
