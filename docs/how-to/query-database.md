# Query database

```php
<?php

use Notion\Notion;
use Notion\Databases\Query;
use Notion\Databases\Query\CompoundFilter;
use Notion\Databases\Query\DateFilter;
use Notion\Databases\Query\Sort;
use Notion\Databases\Query\TextFilter;

$token = $_ENV["NOTION_SECRET"];

$notion = Notion::create($token);

$databaseId = "c986d7b0-7051-4f18-b165-cc0b9503ffc2";
$database = $notion->databases()->find($pageId);

$query = Query::create()
    ->withFilter(
        CompoundFilter::and(
            DateFilter::createdTime::pastWeek(),
            TextFilter::property("Name")->contains("John"),
        )
    )
    ->withAddedSort(Sort::property("Name")->ascending())
    ->withPageSize(20);

$result = $notion->databases()->query($database, $query);

$pages = $result->pages(); // array of Page
$result->hasMore(); // true or false
$result->nextCursor() // cursor ID or null
```