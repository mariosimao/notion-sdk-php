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

## Insert content after a specific block

By default, new blocks are appended at the end of the page. Pass the ID of an
existing child block as the third argument to insert the new blocks right after it.

```php
$siblingBlockId = "0d253ab0-f4c0-4c9d-a1b7-6e2a6c9a1b2c";

$notion->blocks()->append($pageId, $content, $siblingBlockId);
```