# Delete a page

Deleted pages are archived. It is possible to recover archived pages.

```php
<?php

use Notion\Notion;

$token = $_ENV["NOTION_SECRET"];

$notion = Notion::create($token);

$pageId = "c986d7b0-7051-4f18-b165-cc0b9503ffc2";
$page = $notion->pages()->find($pageId);
$page = $notion->pages()->delete($page);

$page->archived(); // true
```