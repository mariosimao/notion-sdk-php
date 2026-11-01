<?php

namespace Notion\Views;

use Notion\Configuration;
use Notion\Infrastructure\Http;
use Notion\Views\Configuration\DashboardConfiguration;
use Notion\Views\Query\ViewQuery;
use Notion\Views\Query\ViewQueryResult;

/**
 * @psalm-import-type ViewJson from View
 * @psalm-import-type ViewListJson from ViewList
 * @psalm-import-type ViewQueryJson from ViewQuery
 * @psalm-import-type ViewQueryResultJson from ViewQueryResult
 */
final readonly class Client
{
    public const MAX_PAGE_SIZE = 100;

    /**
     * @internal Use `\Notion\Notion::views()` instead
     */
    public function __construct(
        private Configuration $config,
    ) {
    }

    /** List a page of view IDs of a database or data source */
    public function list(ListQuery $query): ViewList
    {
        $url = "https://api.notion.com/v1/views?" . http_build_query($query->toArray());
        $request = Http::createRequest($url, $this->config);

        /** @psalm-var ViewListJson $body */
        $body = Http::sendRequest($request, $this->config);

        return ViewList::fromArray($body);
    }

    /**
     * List all view IDs of a database or data source, handling pagination
     *
     * @return string[]
     */
    public function listAll(ListQuery $query): array
    {
        $query = $query->changePageSize(ListQuery::MAX_PAGE_SIZE);

        $viewIds = [];
        do {
            $list = $this->list($query);
            $viewIds = [ ...$viewIds, ...$list->viewIds ];

            if ($list->nextCursor !== null) {
                $query = $query->changeStartCursor($list->nextCursor);
            }
        } while ($list->hasMore && $list->nextCursor !== null);

        return $viewIds;
    }

    public function find(string $viewId): View
    {
        $url = "https://api.notion.com/v1/views/{$viewId}";
        $request = Http::createRequest($url, $this->config);

        /** @psalm-var ViewJson $body */
        $body = Http::sendRequest($request, $this->config);

        return View::fromArray($body);
    }

    public function create(View $view, ViewTarget $target): View
    {
        $data = [
            "data_source_id" => $view->dataSourceId,
            "name"           => $view->name,
            "type"           => $view->type->value,
        ];

        if ($view->filter !== null) {
            $data["filter"] = $view->filter->toArray();
        }
        if ($view->sorts !== []) {
            $data["sorts"] = $view->sortsToArray();
        }
        if ($view->quickFilters !== []) {
            $data["quick_filters"] = $view->quickFiltersToArray();
        }
        if ($view->configuration !== null && !($view->configuration instanceof DashboardConfiguration)) {
            $data["configuration"] = $view->configuration->toArray();
        }

        $data = [ ...$data, ...$target->toArray() ];

        $url = "https://api.notion.com/v1/views";
        $request = Http::createRequest($url, $this->config)
            ->withMethod("POST")
            ->withHeader("Content-Type", "application/json");
        $request->getBody()->write(json_encode($data));

        /** @psalm-var ViewJson $body */
        $body = Http::sendRequest($request, $this->config);

        return View::fromArray($body);
    }

    public function update(View $view): View
    {
        $data = [
            "name"   => $view->name,
            "filter" => $view->filter?->toArray(),
            "sorts"  => $view->sorts === [] ? null : $view->sortsToArray(),
        ];

        $quickFilters = $view->quickFiltersToArray();
        foreach ($view->removedQuickFilters() as $property) {
            if (!array_key_exists($property, $quickFilters)) {
                $quickFilters[$property] = null;
            }
        }
        if ($quickFilters !== []) {
            $data["quick_filters"] = $quickFilters;
        }

        if ($view->configuration !== null && !($view->configuration instanceof DashboardConfiguration)) {
            $data["configuration"] = $view->configuration->toArray();
        }

        $url = "https://api.notion.com/v1/views/{$view->id}";
        $request = Http::createRequest($url, $this->config)
            ->withMethod("PATCH")
            ->withHeader("Content-Type", "application/json");
        $request->getBody()->write(json_encode($data));

        /** @psalm-var ViewJson $body */
        $body = Http::sendRequest($request, $this->config);

        return View::fromArray($body);
    }

    public function delete(View|string $view): void
    {
        $viewId = $view instanceof View ? $view->id : $view;

        $url = "https://api.notion.com/v1/views/{$viewId}";
        $request = Http::createRequest($url, $this->config)
            ->withMethod("DELETE");

        Http::sendRequest($request, $this->config);
    }

    /**
     * Execute the view filters and sorts, caching the results for ~15 minutes.
     *
     * The returned query contains the first page of results. Use `queryResults()`
     * to fetch next pages.
     */
    public function query(View|string $view, int|null $pageSize = null): ViewQuery
    {
        $viewId = $view instanceof View ? $view->id : $view;

        $data = [];
        if ($pageSize !== null) {
            $data["page_size"] = $pageSize;
        }

        $url = "https://api.notion.com/v1/views/{$viewId}/queries";
        $request = Http::createRequest($url, $this->config)
            ->withMethod("POST")
            ->withHeader("Content-Type", "application/json");
        $request->getBody()->write(json_encode((object) $data));

        /** @psalm-var ViewQueryJson $body */
        $body = Http::sendRequest($request, $this->config);

        return ViewQuery::fromArray($body);
    }

    /** Fetch a page of results of a cached view query */
    public function queryResults(
        ViewQuery $query,
        string|null $startCursor = null,
        int|null $pageSize = null,
    ): ViewQueryResult {
        $params = [];
        if ($startCursor !== null) {
            $params["start_cursor"] = $startCursor;
        }
        if ($pageSize !== null) {
            $params["page_size"] = $pageSize;
        }

        $url = "https://api.notion.com/v1/views/{$query->viewId}/queries/{$query->id}";
        if ($params !== []) {
            $url .= "?" . http_build_query($params);
        }
        $request = Http::createRequest($url, $this->config);

        /** @psalm-var ViewQueryResultJson $body */
        $body = Http::sendRequest($request, $this->config);

        return ViewQueryResult::fromArray($body);
    }

    /** Delete a cached view query. Deleting an already deleted or expired query is a no-op. */
    public function deleteQuery(ViewQuery $query): void
    {
        $url = "https://api.notion.com/v1/views/{$query->viewId}/queries/{$query->id}";
        $request = Http::createRequest($url, $this->config)
            ->withMethod("DELETE");

        Http::sendRequest($request, $this->config);
    }

    /**
     * Query a view and fetch the IDs of all pages matching its filters, in the view's sort order.
     *
     * Use `\Notion\Notion::pages()->find()` to retrieve page details.
     *
     * @return string[]
     */
    public function queryAllPages(View|string $view): array
    {
        $query = $this->query($view, self::MAX_PAGE_SIZE);

        try {
            $pageIds = $query->pageIds;
            $hasMore = $query->hasMore;
            $cursor = $query->nextCursor;

            while ($hasMore && $cursor !== null) {
                $result = $this->queryResults($query, $cursor, self::MAX_PAGE_SIZE);
                $pageIds = [ ...$pageIds, ...$result->pageIds ];
                $hasMore = $result->hasMore;
                $cursor = $result->nextCursor;
            }
        } finally {
            $this->deleteQuery($query);
        }

        return $pageIds;
    }
}
