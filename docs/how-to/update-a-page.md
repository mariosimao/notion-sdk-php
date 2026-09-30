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

$notion->pages()->update($page);
```

## Apply a template

```php
<?php

use Notion\Notion;
use Notion\Pages\PageTemplate;

$token = $_ENV["NOTION_SECRET"];
$notion = Notion::create($token);

$pageId = "c986d7b0-7051-4f18-b165-cc0b9503ffc2";
$page = $notion->pages()->find($pageId);

// Template content is appended to the existing content
$notion->pages()->update($page, template: PageTemplate::default());

// Existing content is replaced by the template content
$notion->pages()->update($page, template: PageTemplate::fromId("a7e80c0b-a766-43c3-a9e9-21ce94595e0e"), eraseContent: true);
```

## Erase content

::: warning
Erasing content is irreversible.
:::

```php
<?php

use Notion\Notion;

$token = $_ENV["NOTION_SECRET"];
$notion = Notion::create($token);

$pageId = "c986d7b0-7051-4f18-b165-cc0b9503ffc2";
$page = $notion->pages()->find($pageId);

$notion->pages()->update($page, eraseContent: true);
```
