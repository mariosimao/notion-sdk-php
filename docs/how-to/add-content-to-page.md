# Add content to page

```php
<?php

use Notion\Blocks\Paragraph;
use Notion\Notion;

$token = $_ENV["NOTION_SECRET"];
$notion = Notion::create($token);

$pageId = "c986d7b0-7051-4f18-b165-cc0b9503ffc2";

$content = [
    Paragraph::fromString("This paragraph will be appended."),
    Paragraph::fromString("This other paragraph too!"),
];

$notion->blocks()->append($pageId, $content);
```

## Choose where content is inserted

By default, new blocks are appended at the end of the page. Pass a `Position`
as the third argument to control where the new blocks are inserted.

```php
use Notion\Blocks\Position;

// Insert right after an existing child block
$siblingBlockId = "0d253ab0-f4c0-4c9d-a1b7-6e2a6c9a1b2c";
$notion->blocks()->append($pageId, $content, Position::afterBlock($siblingBlockId));

// Insert at the beginning of the page
$notion->blocks()->append($pageId, $content, Position::start());

// Insert at the end of the page (default)
$notion->blocks()->append($pageId, $content, Position::end());
```
