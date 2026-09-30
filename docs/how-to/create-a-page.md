# Create a page

## Empty page

```php
<?php

use Notion\Notion;
use Notion\Common\Emoji;
use Notion\Pages\Page;
use Notion\Pages\PageParent;

$token = $_ENV["NOTION_SECRET"];
$notion = Notion::create($token);

$parent = PageParent::page("c986d7b0-7051-4f18-b165-cc0b9503ffc2");
$page = Page::create($parent)
            ->changeTitle("Empty page")
            ->changeIcon(Emoji::fromString("⭐"));

$page = $notion->pages()->create($page);
```

## Page with content

```php
<?php

use Notion\Blocks\Heading1;
use Notion\Notion;
use Notion\Blocks\ToDo;
use Notion\Common\Emoji;
use Notion\Pages\Page;
use Notion\Pages\PageParent;

$token = $_ENV["NOTION_SECRET"];
$notion = Notion::create($token);

$parent = PageParent::page("c986d7b0-7051-4f18-b165-cc0b9503ffc2");
$page = Page::create($parent)
            ->changeTitle("Shopping list")
            ->changeIcon(Emoji::fromString("🛒"));

$content = [
    Heading1::fromString("Supermarket"),
    ToDo::fromString("Tomato"),
    ToDo::fromString("Sugar"),
    ToDo::fromString("Apple"),
    ToDo::fromString("Milk"),
    Heading1::fromString("Mall"),
    ToDo::fromString("Black T-shirt"),
];

$page = $notion->pages()->create($page, $content);
```

## Page from a template

Templates are applied asynchronously by Notion, so the returned page is initially blank.
Content cannot be combined with a template.

```php
<?php

use Notion\Notion;
use Notion\Pages\Page;
use Notion\Pages\PageParent;
use Notion\Pages\PageTemplate;

$token = $_ENV["NOTION_SECRET"];
$notion = Notion::create($token);

$dataSourceId = "c986d7b0-7051-4f18-b165-cc0b9503ffc2";
$templates = $notion->dataSources()->listTemplates($dataSourceId, name: "Bug report");

$page = Page::create(PageParent::dataSource($dataSourceId))
            ->changeTitle("Login button does not work");

// A specific template
$template = PageTemplate::fromTemplate($templates->templates[0]);

// Or the data source default template, resolving `@now` and `@today` in a given timezone
$template = PageTemplate::default("America/New_York");

$page = $notion->pages()->create($page, template: $template);
```
