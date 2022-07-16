# Update a page

```php
<?php

use Notion\Notion;
use Notion\Common\Emoji;

$token = $_ENV["NOTION_SECRET"];
$notion = Notion::create($token);

$pageId = "c986d7b0-7051-4f18-b165-cc0b9503ffc2";
$page = $notion->pages()->find($pageId);
$page = $page->withTitle("New title")
             ->withIcon(Emoji::create(🚲));

$notion->pages()->update($page);
```