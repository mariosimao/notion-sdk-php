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
$pageComments = $notion->comments()->list($pageId);
$blockComments = $notion->comments()->list($blockId);

foreach ($pageComments as $comment) {
    echo RichText::multipleToString($comment);
}
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
