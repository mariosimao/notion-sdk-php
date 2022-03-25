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