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
             ->changeIcon(Emoji::fromString(🚲));

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
$page = $page->addProperty("Release date", $updatedRelease);

$notion->pages()->update($page);
```

## Limit the properties returned

`create()` and `update()` accept a list of property names or IDs to include
in the returned page. Other properties are left out of the response. The
request body is not affected.

```php
<?php

use Notion\Notion;

$token = $_ENV["NOTION_SECRET"];
$notion = Notion::create($token);

$pageId = "c986d7b0-7051-4f18-b165-cc0b9503ffc2";
$page = $notion->pages()->find($pageId);
$page = $page->changeTitle("New title");

// Only the "title" and "Status" properties are returned
$page = $notion->pages()->update($page, ["title", "Status"]);

// The same is available when creating pages
$newPage = $notion->pages()->create($page, [], ["title"]);
```
