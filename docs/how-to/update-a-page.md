# Update a page

## Update title

```php
<?php

use Notion\Notion;
use Notion\Common\Emoji;

$token = $_ENV["NOTION_SECRET"];
$notion = Notion::create($token);

$pageId = "c986d7b0-7051-4f18-b165-cc0b9503ffc2";
$page = $notion->pages()->find($pageId);
$page = $page->changeTitle("New title")
             ->changeIcon(Emoji::create(🚲));

$notion->pages()->update($page);
```

## Update properties

```php
<?php

use Notion\Notion;

$token = $_ENV["NOTION_SECRET"];
$notion = Notion::create($token);

$pageId = "c986d7b0-7051-4f18-b165-cc0b9503ffc2";
$page = $notion->pages()->find($pageId);

$updatedRelease = \Notion\Pages\Properties\Date::create(new DateTimeImmutable("2008-11-04"));
$updatedPage = $page->addProperty("Release date", $updatedRelease);

$notion->pages()->update($updatedPage);
```
