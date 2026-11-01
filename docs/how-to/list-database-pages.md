# List data source pages

```php
<?php

use Notion\Notion;

$token = $_ENV["NOTION_SECRET"];
$notion = Notion::create($token);

$dataSourceId = "c986d7b0-7051-4f18-b165-cc0b9503ffc2";
$dataSource = $notion->dataSources()->find($dataSourceId);

$pages = $notion->dataSources()->queryAll($dataSource);

count($pages);
```

A single query returns at most 10,000 pages. `queryAll()` and `iterateAll()`
read past that limit by splitting the data source into `created_time` windows,
and never return the same page twice.

## Filters and sorts

Pass a `Query` to keep only matching pages. The filter is always preserved.
Sorts are honored while the result fits in a single query (up to 10,000
pages); past the limit, the remaining pages come in `created_time` order.

```php
use Notion\DataSources\Query;
use Notion\DataSources\Query\SelectFilter;
use Notion\DataSources\Query\Sort;

$query = Query::create()
    ->changeFilter(SelectFilter::property("Category")->equals("Drama"))
    ->addSort(Sort::property("Release date")->descending());

$pages = $notion->dataSources()->queryAll($dataSource, $query);
```

Filters with a top-level `or` cannot be windowed. If such a query reaches the
limit, a `Notion\Exceptions\QueryException` is thrown: run one query per `or`
branch and merge the results instead.

## Large data sources

`iterateAll()` returns a lazy generator. Pages are fetched on demand, so the
whole data source is never held in memory.

```php
foreach ($notion->dataSources()->iterateAll($dataSource, $query) as $page) {
    // Do something with $page.
}
```

## Detecting incomplete results

When calling `query()` directly, check whether a response was cut off by the
result limit:

```php
$result = $notion->dataSources()->query($dataSource, $query);

$result->isIncomplete(); // true when the 10,000 results limit was reached
$result->requestStatus?->incompleteReason; // "query_result_limit_reached"
```
