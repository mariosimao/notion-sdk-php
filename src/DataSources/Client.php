<?php

namespace Notion\DataSources;

use Notion\Configuration;
use Notion\DataSources\Query\Result;
use Notion\DataSources\Query\Sort;
use Notion\Infrastructure\Http;
use Notion\Pages\Page;

/**
 * @psalm-import-type DataSourceJson from DataSource
 * @psalm-import-type QueryResultJson from Result
 */
class Client
{
    /**
     * @internal Use `\Notion\Notion::dataSources()` instead
     */
    public function __construct(
        private readonly Configuration $config,
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

    public function delete(DataSource $dataSource): void
    {
        $dataSourceId = $dataSource->id;
        $url = "https://api.notion.com/v1/blocks/{$dataSourceId}";
        $request = Http::createRequest($url, $this->config)
            ->withMethod("DELETE");

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
     * @param Sort[] $sorts
     *
     * @return Page[]
     */
    public function queryAllPages(DataSource $dataSource, array $sorts = []): array
    {
        $query = Query::create()
                    ->changeSorts(...$sorts)
                    ->changePageSize(Query::MAX_PAGE_SIZE);

        $pages = [];
        $startCursor = null;
        $hasMore = true;

        while ($hasMore) {
            if ($startCursor !== null) {
                $query = $query->changeStartCursor($startCursor);
            }

            $result = $this->query($dataSource, $query);

            $pages = array_merge($pages, $result->pages);
            $hasMore = $result->hasMore;
            $startCursor = $result->nextCursor;
        }

        return $pages;
    }
}
