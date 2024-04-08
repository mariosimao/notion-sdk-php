<h1 align="center">notion-sdk-php</h1>
<p align="center">A complete Notion SDK for PHP developers.</p>

<p align="center">
<a href="https://mario.engineering/notion-sdk-php">
    <img src="./docs/public/logo.png" width="300">
</a>
</p>

<p align="center">
    <a href="https://app.codecov.io/gh/mariosimao/notion-sdk-php">
        <image src="https://img.shields.io/codecov/c/github/mariosimao/notion-sdk-php?token=ZKKCWDY4QX">
    </a>
    <a href="https://shepherd.dev/github/mariosimao/notion-sdk">
        <image src="https://shepherd.dev/github/mariosimao/notion-sdk/coverage.svg">
    </a>
    <a href="https://developers.notion.com/reference/versioning">
        <image src="https://img.shields.io/badge/API%20Version-2022--06--28-%23212121">
    </a>
    <a href="https://packagist.org/packages/mariosimao/notion-sdk-php">
        <image src="https://img.shields.io/packagist/php-v/mariosimao/notion-sdk-php?color=%23787CB5">
    </a>
    <a href="https://packagist.org/packages/mariosimao/notion-sdk-php">
        <image src="https://img.shields.io/packagist/dt/mariosimao/notion-sdk-php?color=%23FF8A65">
    </a>
</p>


## 📦 Installation

This project requires PHP 8.1 or higher. To install it with Composer run:

```bash
$ composer require mariosimao/notion-sdk-php
```

## 👩‍💻 Basic usage

Creating a page on Notion with the SDK is easy.

```php
use Notion\Blocks\Heading1;
use Notion\Blocks\ToDo;
use Notion\Common\Emoji;
use Notion\Notion;
use Notion\Pages\Page;
use Notion\Pages\PageParent;

$notion = Notion::create("secret_token");

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

## 📄 Documentation

Further documentation can be found at https://mariosimao.github.io/notion-sdk-php.

The Notion PHP SDK supports the usage of static analysers. We strongly recommend the usage of
either [vimeo/psalm](https://github.com/vimeo/psalm) or [phpstan/phpstan](https://github.com/phpstan/phpstan) in combination with this library, to avoid simple mistakes.

## 🏷️ Versioning

[SemVer](semver.org) is followed closely. Minor and patch releases should not introduce breaking changes to the codebase.

Any classes or methods marked as `@internal` are not intended for use outside of this library and are subject to breaking changes at any time, avoid using them.

## 🛠️ Maintenance & Support
When a new minor version (e.g. 1.3 -> 1.4) is released, the previous one (1.3) will continue to receive security and critical bug fixes for at least 3 months.

When a new major version is released (e.g. 1.6 -> 2.0), the previous one (1.6) will receive critical bug fixes for at least 3 months and security updates for 6 months after that new release comes out.

This policy may change in the future and exceptions may be made on a case-by-case basis.

## ❤️ Sponsors

An special thanks to all sponsors who activelly support the SDK!

<!-- sponsors --><!-- sponsors -->
