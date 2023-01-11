<?php

namespace Notion\Databases;

use Notion\Configuration;
use Notion\Databases\Query\Result;
use Notion\Databases\Query\Sort;
use Notion\Infrastructure\Http;
use Notion\Pages\Page;

/**
 * @psalm-import-type DatabaseJson from Database
 * @psalm-import-type QueryResultJson from Result
 */
class Client
{
    /**
     * @internal Use `\Notion\Notion::databases()` instead
     */
    public function __construct(
        private readonly Configuration $config,
    ) {
    }

    public function find(string $databaseId): Database
    {
        $url = "https://api.notion.com/v1/databases/{$databaseId}";
        $request = Http::createRequest($url, $this->config);

        /** @psalm-var DatabaseJson $body */
        $body = Http::sendRequest($request, $this->config);

        return Database::fromArray($body);
    }

    public function create(Database $database): Database
    {
        $data = $database->toArray();
        unset($data["id"]);

        $url = "https://api.notion.com/v1/databases";
        $request = Http::createRequest($url, $this->config)
            ->withMethod("POST")
            ->withHeader("Content-Type", "application/json");
        $request->getBody()->write(json_encode($data));

        /** @psalm-var DatabaseJson $body */
        $body = Http::sendRequest($request, $this->config);

        return Database::fromArray($body);
    }

    public function update(Database $database): Database
    {
        $data = $database->toArray();
        unset($data["parent"]);
        unset($data["created_time"]);
        unset($data["last_edited_time"]);

        $databaseId = $database->id;
        $url = "https://api.notion.com/v1/databases/{$databaseId}";
        $request = Http::createRequest($url, $this->config)
            ->withMethod("PATCH")
            ->withHeader("Content-Type", "application/json");

        $request->getBody()->write(json_encode($data));

        /** @psalm-var DatabaseJson $body */
        $body = Http::sendRequest($request, $this->config);

        return Database::fromArray($body);
    }

    public function delete(Database $database): void
    {
        $databaseId = $database->id;
        $url = "https://api.notion.com/v1/blocks/{$databaseId}";
        $request = Http::createRequest($url, $this->config)
            ->withMethod("DELETE");

        Http::sendRequest($request, $this->config);
    }

    public function query(Database $database, Query $query): Result
    {
        $data = $query->toArray();

        $databaseId = $database->id;
        $url = "https://api.notion.com/v1/databases/{$databaseId}/query";
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
    public function queryAllPages(Database $database, array $sorts = []): array
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

            $result = $this->query($database, $query);

            $pages = array_merge($pages, $result->pages);
            $hasMore = $result->hasMore;
            $startCursor = $result->nextCursor;
        }

        return $pages;
    }
}
