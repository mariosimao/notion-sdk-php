<?php

namespace Notion\DataSources;

use DateTimeImmutable;
use Generator;
use Notion\Common\Date;
use Notion\Configuration;
use Notion\DataSources\Query\CompoundFilter;
use Notion\DataSources\Query\DateFilter;
use Notion\DataSources\Query\Filter;
use Notion\DataSources\Query\Result;
use Notion\DataSources\Query\Sort;
use Notion\Exceptions\QueryException;
use Notion\Infrastructure\Http;
use Notion\Pages\Page;

/**
 * @psalm-import-type DataSourceJson from DataSource
 * @psalm-import-type QueryResultJson from Result
 */
final readonly class Client
{
    /**
     * @internal Use `\Notion\Notion::dataSources()` instead
     */
    public function __construct(
        private Configuration $config,
    ) {
    }

    public function find(string $dataSourceId): DataSource
    {
        $url = "https://api.notion.com/v1/data_sources/{$dataSourceId}";
        $request = Http::createRequest($url, $this->config);

        /** @psalm-var DataSourceJson $body */
        $body = Http::sendRequest($request, $this->config);

        return DataSource::fromArray($body);
    }

    public function create(DataSource $dataSource): DataSource
    {
        $data = $dataSource->toArray();
        unset($data["id"]);
        unset($data["database_parent"]);
        unset($data["created_by"]);
        unset($data["last_edited_by"]);
        if ($dataSource->parent->isDatabase()) {
            unset($data["description"]);
        }

        $url = "https://api.notion.com/v1/data_sources";
        $request = Http::createRequest($url, $this->config)
            ->withMethod("POST")
            ->withHeader("Content-Type", "application/json");
        $request->getBody()->write(json_encode($data));

        /** @psalm-var DataSourceJson $body */
        $body = Http::sendRequest($request, $this->config);

        return DataSource::fromArray($body);
    }

    public function update(DataSource $dataSource): DataSource
    {
        $data = $dataSource->toArray();
        unset($data["parent"]);
        unset($data["created_time"]);
        unset($data["last_edited_time"]);
        unset($data["created_by"]);
        unset($data["last_edited_by"]);
        unset($data["database_parent"]);
        if ($dataSource->parent->isDatabase()) {
            unset($data["description"]);
        }

        $dataSourceId = $dataSource->id;
        $url = "https://api.notion.com/v1/data_sources/{$dataSourceId}";
        $request = Http::createRequest($url, $this->config)
            ->withMethod("PATCH")
            ->withHeader("Content-Type", "application/json");

        $request->getBody()->write(json_encode($data));

        /** @psalm-var DataSourceJson $body */
        $body = Http::sendRequest($request, $this->config);

        return DataSource::fromArray($body);
    }

    public function move(DataSource $dataSource, string $databaseId): DataSource
    {
        $dataSourceId = $dataSource->id;
        $url = "https://api.notion.com/v1/data_sources/{$dataSourceId}";
        $request = Http::createRequest($url, $this->config)
            ->withMethod("PATCH")
            ->withHeader("Content-Type", "application/json");

        $request->getBody()->write(json_encode([
            "parent" => DataSourceParent::database($databaseId)->toArray(),
        ]));

        /** @psalm-var DataSourceJson $body */
        $body = Http::sendRequest($request, $this->config);

        return DataSource::fromArray($body);
    }

    public function delete(DataSource $dataSource): void
    {
        $dataSourceId = $dataSource->id;
        $url = "https://api.notion.com/v1/data_sources/{$dataSourceId}";
        $request = Http::createRequest($url, $this->config)
            ->withMethod("PATCH")
            ->withHeader("Content-Type", "application/json");

        $request->getBody()->write(json_encode([
            "in_trash" => true,
        ]));

        Http::sendRequest($request, $this->config);
    }

    public function query(DataSource $dataSource, Query $query): Result
    {
        $data = $query->toArray();

        $dataSourceId = $dataSource->id;
        $url = "https://api.notion.com/v1/data_sources/{$dataSourceId}/query";
        $request = Http::createRequest($url, $this->config)
            ->withMethod("POST")
            ->withHeader("Content-Type", "application/json");

        $request->getBody()->write(json_encode($data));

        /** @psalm-var QueryResultJson $body */
        $body = Http::sendRequest($request, $this->config);

        return Result::fromArray($body);
    }

    /**
     * Lazily iterate over every page matching a query, including pages past
     * the per-query result limit (10,000 rows).
     *
     * A single query stops paginating once it reaches the result limit. To
     * read every row, this method partitions the data source into
     * `created_time` windows: it sorts by `created_time` ascending and, every
     * time a window reaches the limit, starts a fresh query from the last
     * row's `created_time`. Rows sharing the boundary timestamp are
     * de-duplicated by ID, so every page is yielded exactly once.
     *
     * The query filter is preserved and combined with the window bound. Its
     * start cursor is ignored, as pagination is handled automatically.
     *
     * The query sorts are honored while the result fits in a single query. If
     * the limit is reached, the remaining pages are read with `created_time`
     * windows, skipping pages already yielded, so they are not in sort order.
     *
     * @throws QueryException If the limit is reached and the query has a
     *   top-level "or" filter, or if more rows than the limit share the same
     *   `created_time` (add a filter to narrow the query in that case).
     *
     * @return Generator<int, Page, mixed, null>
     */
    public function iterateAll(DataSource $dataSource, Query|null $query = null): Generator
    {
        $query ??= Query::create();
        $filter = $query->filter;

        /** @var array<string, true> $seenPageIds */
        $seenPageIds = [];

        if ($query->sorts !== [] && !self::isCreatedTimeAscending($query->sorts)) {
            $limitReached = false;
            $firstQuery = self::windowQuery($query, $filter, $query->sorts);
            foreach ($this->paginate($dataSource, $firstQuery) as $result) {
                foreach ($result->pages as $page) {
                    if (!isset($seenPageIds[$page->id])) {
                        $seenPageIds[$page->id] = true;
                        yield $page;
                    }
                }
                $limitReached = $limitReached || $result->isIncomplete();
            }

            if (!$limitReached) {
                return;
            }
        }

        $windowStart = null;
        while (true) {
            $limitReached = false;
            $lastCreatedTime = null;

            $windowQuery = self::windowQuery(
                $query,
                self::windowFilter($filter, $windowStart),
                [ Sort::createdTime()->ascending() ],
            );

            foreach ($this->paginate($dataSource, $windowQuery) as $result) {
                foreach ($result->pages as $page) {
                    $lastCreatedTime = $page->createdTime;
                    if (!isset($seenPageIds[$page->id])) {
                        $seenPageIds[$page->id] = true;
                        yield $page;
                    }
                }
                $limitReached = $limitReached || $result->isIncomplete();
            }

            if (!$limitReached) {
                return;
            }

            if ($lastCreatedTime === null || ($windowStart !== null && $lastCreatedTime <= $windowStart)) {
                throw QueryException::windowCannotAdvance($lastCreatedTime?->format(Date::FORMAT));
            }

            $windowStart = $lastCreatedTime;
        }
    }

    /**
     * Collect every page matching a query, including pages past the
     * per-query result limit (10,000 rows).
     *
     * See `iterateAll()` for how the limit, filters and sorts are handled.
     * For very large data sources prefer `iterateAll()`, which streams pages
     * instead of holding all of them in memory.
     *
     * @throws QueryException
     *
     * @return list<Page>
     */
    public function queryAll(DataSource $dataSource, Query|null $query = null): array
    {
        return iterator_to_array($this->iterateAll($dataSource, $query), false);
    }

    /** @return Generator<int, Result, mixed, void> */
    private function paginate(DataSource $dataSource, Query $query): Generator
    {
        do {
            $result = $this->query($dataSource, $query);
            yield $result;

            $nextCursor = $result->hasMore ? $result->nextCursor : null;
            if ($nextCursor !== null) {
                $query = $query->changeStartCursor($nextCursor);
            }
        } while ($nextCursor !== null);
    }

    /** @param Sort[] $sorts */
    private static function windowQuery(Query $query, Filter|null $filter, array $sorts): Query
    {
        $windowQuery = Query::create()
            ->changePageSize($query->pageSize)
            ->changeSorts(...$sorts);

        if ($filter !== null) {
            $windowQuery = $windowQuery->changeFilter($filter);
        }

        return $windowQuery;
    }

    private static function windowFilter(Filter|null $filter, DateTimeImmutable|null $windowStart): Filter|null
    {
        if ($windowStart === null) {
            return $filter;
        }

        $bound = DateFilter::createdTime()->onOrAfter($windowStart);

        if ($filter === null) {
            return $bound;
        }

        if ($filter instanceof CompoundFilter) {
            if ($filter->isOr()) {
                throw QueryException::topLevelOrFilterCannotBeWindowed();
            }

            return CompoundFilter::and(...$filter->filters(), ...[ $bound ]);
        }

        return CompoundFilter::and($filter, $bound);
    }

    /** @param Sort[] $sorts */
    private static function isCreatedTimeAscending(array $sorts): bool
    {
        return count($sorts) === 1
            && $sorts[0]->toArray() === Sort::createdTime()->ascending()->toArray();
    }
}
