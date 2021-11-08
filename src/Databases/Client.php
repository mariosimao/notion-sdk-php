<?php

namespace Notion\Databases;

use Notion\NotionException;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;

/**
 * @psalm-import-type DatabaseJson from Database
 */
class Client
{
    private ClientInterface $psrClient;
    private RequestFactoryInterface $requestFactory;
    private string $token;
    private string $version;

    /**
     * @internal Use `\Notion\Notion::databases()` instead
     */
    public function __construct(
        ClientInterface $psrClient,
        RequestFactoryInterface $requestFactory,
        string $token,
        string $version
    ) {
        $this->psrClient = $psrClient;
        $this->requestFactory = $requestFactory;
        $this->token = $token;
        $this->version = $version;
    }

    public function find(string $databaseId): Database
    {
        $url = "https://api.notion.com/v1/databases/{$databaseId}";
        $request = $this->requestFactory->createRequest("GET", $url)
            ->withHeader("Authorization", "Bearer {$this->token}")
            ->withHeader("Notion-Version", $this->version);

        $response = $this->psrClient->sendRequest($request);

        /** @var array */
        $body = json_decode((string) $response->getBody(), true);

        if ($response->getStatusCode() !== 200) {
            /** @var array{ message: string, code: string} $body */
            $message = $body["message"];
            $code = $body["code"];

            throw new NotionException($message, $code);
        }

        /** @psalm-var DatabaseJson $body */
        return Database::fromArray($body);
    }

    public function create(Database $database): Database
    {
        $data = $database->toArray();
        unset($data["id"]);

        $url = "https://api.notion.com/v1/databases";
        $request = $this->requestFactory->createRequest("POST", $url)
            ->withHeader("Authorization", "Bearer {$this->token}")
            ->withHeader("Notion-Version", $this->version)
            ->withHeader("Content-Type", "application/json");

        $request->getBody()->write(json_encode($data));

        $response = $this->psrClient->sendRequest($request);

        /** @var array */
        $body = json_decode((string) $response->getBody(), true);

        if ($response->getStatusCode() !== 200) {
            /** @var array{ message: string, code: string} $body */
            $message = $body["message"];
            $code = $body["code"];

            throw new NotionException($message, $code);
        }

        /** @psalm-var DatabaseJson $body */
        return Database::fromArray($body);
    }

    public function update(Database $database): Database
    {
        $data = $database->toArray();
        unset($data["parent"]);
        unset($data["created_time"]);
        unset($data["last_edited_time"]);

        $databaseId = $database->id();
        $url = "https://api.notion.com/v1/databases/{$databaseId}";
        $request = $this->requestFactory->createRequest("PATCH", $url)
            ->withHeader("Authorization", "Bearer {$this->token}")
            ->withHeader("Notion-Version", $this->version)
            ->withHeader("Content-Type", "application/json");

        $request->getBody()->write(json_encode($data));

        $response = $this->psrClient->sendRequest($request);

        /** @var array */
        $body = json_decode((string) $response->getBody(), true);

        if ($response->getStatusCode() !== 200) {
            /** @var array{ message: string, code: string} $body */
            $message = $body["message"];
            $code = $body["code"];

            throw new NotionException($message, $code);
        }

        /** @psalm-var DatabaseJson $body */
        return Database::fromArray($body);
    }

    public function delete(Database $database): void
    {
        $databaseId = $database->id();
        $url = "https://api.notion.com/v1/blocks/{$databaseId}";
        $request = $this->requestFactory->createRequest("DELETE", $url)
            ->withHeader("Authorization", "Bearer {$this->token}")
            ->withHeader("Notion-Version", $this->version);

        $response = $this->psrClient->sendRequest($request);

        if ($response->getStatusCode() !== 200) {
            /** @var array{ message: string, code: string} $body */
            $body = json_decode((string) $response->getBody(), true);
            $message = $body["message"];
            $code = $body["code"];

            throw new NotionException($message, $code);
        }
    }
}
