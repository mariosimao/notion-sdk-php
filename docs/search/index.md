# Search

## Introduction

Searches all parent or child pages and databases that have been shared with an
integration.

Returns all pages or databases, excluding duplicated linked databases, that have
titles that include the query param. If no query param is provided, then the
response contains all pages or databases that have been shared with the
integration.

::: warning
If you want to search a specific individual database, rather than across all
databases, then [query a database](../how-to/query-database.md) instead.
:::

## Searching

```php
use Notion\Search\Query;

$query = Query::title("Example"); // Search by page/database title...
$query = Query::all();            // ... or search everything

$result = $notion->search()->search($query);

$result->hasMore;    // bool
$result->nextCursor; // CursorId when hasMore is true
$result->results;    // array of Page and/or Database objects
```

## Query options

```php
use Notion\Search\Query;

$query = Query::title("Example");                                // Page or database title
$query = $query->filterByPages();                                // Return only pages
$query = $query->filterByDatabases();                            // Return only databases
$query = $query->sortByLastEditedTime(SortDirection::Ascending); // Results order

// Pagination
$query = $query->changePageSize(10);
$query = $query->changeNextCursor("70d73991-7e06-43d9-ad3c-3711213f1235")
```