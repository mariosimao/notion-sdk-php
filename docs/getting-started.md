# Getting started

This section will help you setup a basic Notion application using
`notion-sdk-php` from ground up. If you already have an existing project, start
from [Step 2](#_2-install-notion-sdk-php).

## 1. Create a new project

Create and change into a new directory.

```bash
$ mkdir notion-app && cd notion-app
```

Then, initialize with Composer.

```bash
$ composer init
```

## 2. Install Notion SDK PHP

Add `notion-sdk-php` as dependency for the project.

```bash
$ composer require mariosimao/notion-sdk-php
```

## 3. Get a Notion token

A Notion token will be needed to fully use this library. If you don't have one,
please refer to the [Authorization section](https://developers.notion.com/docs/authorization)
on the [Notion API documentation](https://developers.notion.com/).

## 4. Use the SDK

Test if everything is working by listing all users from the Notion workspace.

```php
<?php

require "vendor/autoload.php";

use Notion\Notion;

$token = "secret_token";
$notion = Notion::create($token);

$users = $notion->users()->findAll();

foreach ($users as $user) {
    echo $user->name() . PHP_EOL;
}
```
