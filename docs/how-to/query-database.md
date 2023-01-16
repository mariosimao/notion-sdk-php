# Query database

It is possible to query a database and set filters

```php
<?php

use Notion\Notion;
use Notion\Databases\Query;
use Notion\Databases\Query\CompoundFilter;
use Notion\Databases\Query\DateFilter;
use Notion\Databases\Query\StatusFilter;
use Notion\Databases\Query\Sort;

$token = $_ENV["NOTION_SECRET"];
$notion = Notion::create($token);

$databaseId = "c986d7b0-7051-4f18-b165-cc0b9503ffc2";
$database = $notion->databases()->find($databaseId);

/**
 * 90s movies not watched
 *
 * Status != Watched AND
 * Release >= 1990-01-01 AND
 * Release <= 1999-12-31
 */
$query = Query::create()
    ->changeFilter(
        CompoundFilter::and(
            StatusFilter::property("Status")->doesNotEqual("Watched"),
            DateFilter::property("Release date")->onOrAfter("1990-01-01"),
            DateFilter::property("Release date")->onOrBefore("1999-12-31"),
        )
    )
    ->addSort(Sort::property("Name")->ascending())  // Optional
    ->changePageSize(20);                           // Optional. Default page size is 100.

$result = $notion->databases()->query($database, $query);

$pages = $result->pages; // array of Pages
$result->hasMore;        // true or false
$result->nextCursor      // cursor ID or null
```
