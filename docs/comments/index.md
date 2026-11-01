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
$comment->body;             // CommentBody
$comment->body->richText;   // RichText array
```

## Read comments

```php
$pageComments = $notion->comments()->list($pageId);
$blockComments = $notion->comments()->list($blockId);

foreach ($pageComments as $comment) {
    echo RichText::multipleToString(...$comment->body->richText);
}
```

## Add page comment

```php
$body = CommentBody::richText(RichText::fromString("A sample page comment."));
$comment = Comment::create($pageId, $body);

$notion->comments()->create($comment);
```

## Add comment on discussion

```php
$body = CommentBody::richText(RichText::fromString("A sample discussion comment."));
$comment = Comment::createReply($discussionId, $body);

$notion->comments()->create($comment);
```

## Markdown comments

Instead of rich text, the comment body can be written as a markdown string.
Comments support inline formatting only (bold, italic, strikethrough, inline
code, links), inline equations and mentions.

```php
$comment = Comment::create($pageId, CommentBody::markdown("**Bold** and *italic*"));
$notion->comments()->create($comment);
```

Notion-flavored Markdown (`MarkdownVersion::V2`) is used by default. The
deprecated enhanced markdown syntax can be selected explicitly:

```php
use Notion\Common\MarkdownVersion;

$body = CommentBody::markdown("*Reply*", MarkdownVersion::V1);
```

Comments returned by Notion always expose their content as rich text in
`$comment->body->richText`.
