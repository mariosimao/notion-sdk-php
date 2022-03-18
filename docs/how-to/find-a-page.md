# Find a page

It is possible to retrieve a page by knowing its ID.

```php
<?php

use Notion\Notion;

$token = $_ENV["NOTION_SECRET"];

$notion = Notion::create($token);

$pageId = "c986d7b0-7051-4f18-b165-cc0b9503ffc2";
$page = $notion->pages()->find($pageId);

echo $page->title()->toString();
```