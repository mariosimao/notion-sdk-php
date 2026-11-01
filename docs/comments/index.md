# Comments

It is possible to comment on pages and blocks.

Comment objects have the following fields:

```php
$comment->id;               // cca26fdc...
$comment->createdTime;      // 2023-01-18
$comment->lastEditedTime;   // 2023-01-18
$comment->userId;           // 3f577044...
$comment->parent->id;       // 41ef05c4...
$comment->parent->type;     // ParentType (block, page, database)
$comment->discussionId;     // 311523ee...
$comment->text;             // RichText array
```

## Read comments

```php
$pageComments = $notion->comments()->findAll($pageId);
$blockComments = $notion->comments()->findAll($blockId);

foreach ($pageComments as $comment) {
    echo RichText::multipleToString(...$comment->text);
}
```

`findAll()` follows pagination and returns every comment. To fetch a single
page of comments, use `list()`:

```php
$list = $notion->comments()->list($pageId, pageSize: 50);

$list->results;    // Comment[]
$list->hasMore;    // true if there are more comments
$list->nextCursor; // pass as $startCursor to fetch the next page

$next = $notion->comments()->list($pageId, $list->nextCursor, 50);
```

## Add page comment

```php
$text = RichText::fromString("A sample page comment.")
$comment = Comment::create($pageId, $text);

$notion->comments()->create($comment);
```

## Add comment on discussion

```php
$text = RichText::fromString("A sample discussion comment.")
$comment = Comment::createReply($discussionId, $text);

$notion->comments()->create($comment);
```
