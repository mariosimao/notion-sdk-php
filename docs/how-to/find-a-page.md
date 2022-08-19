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
::: warning
When finding a page, you will not get the content of it, only meta information and properties.
:::

## Get page content

In Notion, pages behave like blocks where the content is their children.

You can fetch page content with

* `$notion->blocks()->findChildren($pageId)` or
* `$notion->blocks()->findChildrenRecursive($pageId)`

The second option iterates over the content to find also their children (useful for nested content).

```php
<?php

$token = $_ENV["NOTION_TOKEN"];
$notion = \Notion\Notion::create($token);

$pageId = "471373adacbe4247aa4b2ce06ed14026";
$content = $notion->blocks()->findChildren($pageId);
```
