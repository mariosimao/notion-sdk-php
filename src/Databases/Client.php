<?php

namespace Notion\Databases;

use Notion\Common\RichText;
use Notion\Configuration;
use Notion\DataSources\Properties\PropertyInterface;
use Notion\Infrastructure\Http;

/**
 * @psalm-import-type DatabaseJson from Database
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

    /**
     * @param PropertyInterface[] $initialProperties
     */
    public function create(Database $database, array $initialProperties = []): Database
    {
        $data = $database->toArray();
        unset($data["id"]);
        if ($database->icon === null) {
            unset($data["icon"]);
        }
        if ($database->cover === null) {
            unset($data["cover"]);
        }

        $propertiesData = array_map(fn(PropertyInterface $property) => $property->toArray(), $initialProperties);
        if (!empty($propertiesData)) {
            $data["initial_data_source"] = [
                "properties" => $propertiesData,
            ];
        }

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
        unset($data["id"]);
        unset($data["object"]);
        unset($data["created_time"]);
        unset($data["last_edited_time"]);
        unset($data["url"]);
        unset($data["data_sources"]);
        if ($database->icon === null) {
            unset($data["icon"]);
        }
        if ($database->cover === null) {
            unset($data["cover"]);
        }

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

    public function delete(Database $database): Database
    {
        $databaseId = $database->id;
        $url = "https://api.notion.com/v1/databases/{$databaseId}";
        $request = Http::createRequest($url, $this->config)
            ->withMethod("PATCH")
            ->withHeader("Content-Type", "application/json");

        $request->getBody()->write(json_encode([
            "in_trash" => true,
        ]));

        /** @psalm-var DatabaseJson $body */
        $body = Http::sendRequest($request, $this->config);

        return Database::fromArray($body);
    }
}
