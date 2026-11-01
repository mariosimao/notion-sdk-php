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
```

## Multi-value filters

Select and status filters accept a list of values for `equals()` and
`doesNotEqual()`, and multi-select filters accept a list of values for
`contains()` and `doesNotContain()`. Entries matching **any** of the values
are included (or excluded, for the negated conditions).

```php
use Notion\DataSources\Query\MultiSelectFilter;
use Notion\DataSources\Query\SelectFilter;
use Notion\DataSources\Query\StatusFilter;

SelectFilter::property("Priority")->equals(["Low", "Medium"]);
StatusFilter::property("Status")->doesNotEqual(["Done", "Archive"]);
MultiSelectFilter::property("Tags")->contains(["Marketing", "Sales"]);
```
