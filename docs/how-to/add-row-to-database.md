# Add row to database

Database rows on Notion are essentially pages where the parent is the database.

```php
<?php

use Notion\Blocks\Paragraph;
use Notion\Notion;

$token = $_ENV["NOTION_SECRET"];
$notion = Notion::create($token);

$databaseId = "your_database_id_here";
$parent = \Notion\Pages\PageParent::database($databaseId);

$title    = \Notion\Pages\Properties\Title::create("Superbad");
$release  = \Notion\Pages\Properties\Date::create(new DateTimeImmutable("2007-10-19"));
$category = \Notion\Pages\Properties\Select::fromName("Comedy")
    ->withColor(\Notion\Pages\Properties\Option::COLOR_BROWN);

$page = \Notion\Pages\Page::create($parent)
    ->withAddedProperty("Title", $title)
    ->withAddedProperty("Release date", $release)
    ->withAddedProperty("Category", $category);
```