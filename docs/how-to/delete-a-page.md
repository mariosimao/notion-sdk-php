# Delete a page

Deleted pages are moved to trash. It is possible to recover pages in trash.

```php
<?php

use Notion\Notion;

$token = $_ENV["NOTION_SECRET"];
$notion = Notion::create($token);

$pageId = "c986d7b0-7051-4f18-b165-cc0b9503ffc2";
$page = $notion->pages()->find($pageId);
$page = $notion->pages()->delete($page);

$page->inTrash; // true
```