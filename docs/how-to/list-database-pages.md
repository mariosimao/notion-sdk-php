# List database pages

```php
<?php

use Notion\Notion;

$token = $_ENV["NOTION_SECRET"];

$notion = Notion::create($token);

$databaseId = "c986d7b0-7051-4f18-b165-cc0b9503ffc2";
$database = $notion->databases()->find($pageId);

$pages = $notion->databases()->queryAllPages($database);

count($pages);
```