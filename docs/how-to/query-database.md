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

## Relative dates and current user

Date comparisons accept `RelativeDate` values, resolved by Notion at query time,
and people filters accept `CurrentUser::Me` to match the user associated with
the token.

```php
<?php

use Notion\DataSources\Query;
use Notion\DataSources\Query\CompoundFilter;
use Notion\DataSources\Query\CurrentUser;
use Notion\DataSources\Query\DateFilter;
use Notion\DataSources\Query\PeopleFilter;
use Notion\DataSources\Query\RelativeDate;

$query = Query::create()->changeFilter(
    CompoundFilter::and(
        PeopleFilter::property("Assignee")->contains(CurrentUser::Me),
        DateFilter::property("Due date")->onOrAfter(RelativeDate::Today),
        DateFilter::property("Due date")->before(RelativeDate::OneWeekFromNow),
    )
);
```

Available relative dates: `Today`, `Tomorrow`, `Yesterday`, `OneWeekAgo`,
`OneWeekFromNow`, `OneMonthAgo` and `OneMonthFromNow`.

For internal integrations there is no associated user, so
`contains(CurrentUser::Me)` matches no entries and
`doesNotContain(CurrentUser::Me)` matches all entries.
