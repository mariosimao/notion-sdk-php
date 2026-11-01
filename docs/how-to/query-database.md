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
$database = $notion->databases()->find($databaseId);

$query = Query::create()
    ->changeFilter(
        CompoundFilter::and(
            DateFilter::createdTime()->pastWeek(),
            TextFilter::property("Name")->contains("John"),
        )
    )
    ->addSort(Sort::property("Name")->ascending())
    ->changePageSize(20);

$result = $notion->databases()->query($database, $query);

$pages = $result->pages; // array of Page
$result->hasMore; // true or false
$result->nextCursor // cursor ID or null
$result->isIncomplete(); // true when Notion stopped at the 10,000 results limit
$result->requestStatus->incompleteReason; // IncompleteReason::QueryResultLimitReached or null
```

Notion paginates through at most 10,000 results per query. When this limit is
reached, `hasMore` becomes `false` and `isIncomplete()` returns `true`. Narrow
the query with filters (e.g. `created_time` windows) to read the remaining
entries.
