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
$comment->attachments;      // CommentAttachment array
$comment->displayName;      // CommentDisplayName|null
$comment->hasPendingAttachments; // false
```

## Attachments

```php
foreach ($comment->attachments as $attachment) {
    $attachment->category;          // CommentAttachmentCategory (audio, image, pdf, productivity, video)
    $attachment->file->url;         // https://s3.us-west-2.amazonaws.com/...
    $attachment->file->expiryTime;  // DateTimeImmutable
}
```

Files that are still uploading are not listed in `attachments` and
`hasPendingAttachments` is `true` while they upload.

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

## Add comment with attachments

Attach files previously uploaded with `$notion->fileUploads()`.
Comments support up to 3 attachments.

```php
$upload = $notion->fileUploads()->upload("report.pdf");

$comment = Comment::create($pageId, RichText::fromString("See the attached report."))
    ->addAttachment(CommentAttachment::fromFileUpload($upload->id));

$notion->comments()->create($comment);
```

## Custom display name

```php
$comment = Comment::create($pageId, RichText::fromString("Automated response"))
    ->changeDisplayName(CommentDisplayName::custom("Notion Bot"));

$comment = $notion->comments()->create($comment);
$comment->displayName->resolvedName; // Notion Bot
```

Other options are `CommentDisplayName::connection()` (name of the connection)
and `CommentDisplayName::user()` (user who authenticated the connection).
