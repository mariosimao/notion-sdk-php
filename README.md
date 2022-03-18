[![Codecov](https://img.shields.io/codecov/c/github/mariosimao/notion-sdk)](https://app.codecov.io/gh/mariosimao/notion-sdk)
[![Type coverage](https://shepherd.dev/github/mariosimao/notion-sdk/coverage.svg)](https://shepherd.dev/github/mariosimao/notion-sdk)
[![Notion API version](https://img.shields.io/badge/API%20Version-2022--02--22-blue)](https://developers.notion.com/reference/versioning)

# notion-sdk-php

A complete Notion SDK for PHP developers.
## Installation

```
composer require mariosimao/notion-sdk-php
```

## Getting started

A Notion token will be needed to fully use this library. If you don't have one,
please refer to [Authorization section](https://developers.notion.com/docs/authorization) from the [Notion API documentation](https://developers.notion.com/).

A simple example on how to create a page with some content:

```php
<?php

use Notion\Notion;
use Notion\Blocks\Paragraph;
use Notion\Pages\Page;
use Notion\Pages\PageParent;

$token = getenv("NOTION_TOKEN");
$notion = Notion::create($token);

$parent = PageParent::page("c986d7b0-7051-4f18-b165-cc0b9503ffc2");
$page = Page::create($parent)
            ->withTitle("Sample page")
            ->withIcon("⭐");

$content = Paragraph::fromString("This is a simple paragraph.");

$notion->pages()->create($page, $content);
```

## Documentation

Further documentation can be found [here](./docs/README.md).