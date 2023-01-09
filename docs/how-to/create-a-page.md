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
            ->changeIcon(Emoji::create("⭐"));

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
            ->changeIcon(Emoji::create("🛒"));

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
