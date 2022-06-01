<p align="center">
    <img src="./logo.png" width="300">
</p>

<p align="center">
    <a href="https://app.codecov.io/gh/mariosimao/notion-sdk">
        <image src="https://img.shields.io/codecov/c/github/mariosimao/notion-sdk">
    </a>
    <a href="https://shepherd.dev/github/mariosimao/notion-sdk">
        <image src="https://shepherd.dev/github/mariosimao/notion-sdk/coverage.svg">
    </a>
    <a href="https://developers.notion.com/reference/versioning">
        <image src="https://img.shields.io/badge/API%20Version-2022--02--22-blue">
    </a>
    <a href="https://codecov.io/gh/mariosimao/notion-sdk-php">
        <image src="https://codecov.io/gh/mariosimao/notion-sdk-php/branch/main/graph/badge.svg?token=ZKKCWDY4QX">
    </a>
</p>

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
use Notion\Common\Emoji;
use Notion\Blocks\Paragraph;
use Notion\Pages\Page;
use Notion\Pages\PageParent;

$token = $_ENV["NOTION_TOKEN"];
$notion = Notion::create($token);

$parent = PageParent::page("c986d7b0-7051-4f18-b165-cc0b9503ffc2");
$page = Page::create($parent)
            ->withTitle("Sample page")
            ->withIcon(Emoji::create("⭐"));

$content = [
    Paragraph::fromString("This is a simple paragraph."),
];

$notion->pages()->create($page, $content);
```

## Documentation

Further documentation can be found [here](./docs/README.md).