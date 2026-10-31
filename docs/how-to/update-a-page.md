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

## Update icon

Icons can be an emoji, an external file or a native Notion icon.

```php
<?php

use Notion\Notion;
use Notion\Common\NativeIcon;
use Notion\Common\NativeIconColor;

$token = $_ENV["NOTION_SECRET"];
$notion = Notion::create($token);

$pageId = "c986d7b0-7051-4f18-b165-cc0b9503ffc2";
$page = $notion->pages()->find($pageId);
$page = $page->changeIcon(NativeIcon::create("pizza", NativeIconColor::Blue));

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
