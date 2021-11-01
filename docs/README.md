# Documentation

## Installation

```
composer require mariosimao/notion-php
```

## Getting started

A Notion token will be needed to fully use this library. If you don't have one,
please refer to [Authorization section](https://developers.notion.com/docs/authorization) from the [Notion API documentation](https://developers.notion.com/).

A simple example on how to create a page with some content:

```php
<?php

$token = getenv("NOTION_TOKEN");
$client = \Notion\Client::create($token);

$parent = PageParent::page("c986d7b0-7051-4f18-b165-cc0b9503ffc2");
$page = Page::create($parent)
            ->withTitle("Sample page")
            ->withIcon("⭐");

$content = Paragraph::fromString("This is a simple paragraph.");

$client->pages()->create($page, $content);
```