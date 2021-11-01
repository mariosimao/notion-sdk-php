<?php

namespace Notion\Databases;

use Notion\Blocks\BlockInterface;
use Notion\Common\RichText;
use Notion\NotionException;
use Notion\Databases\Properties\PropertyInterface;
use Nyholm\Psr7\Request;
use Psr\Http\Client\ClientInterface;

/**
 * @psalm-import-type DatabaseJson from Database
 */
class Client
{
    private ClientInterface $psrClient;
    private string $token;
    private string $version;

    public function __construct(
        ClientInterface $psrClient,
        string $token,
        string $version
    ) {
        $this->psrClient = $psrClient;
        $this->token = $token;
        $this->version = $version;
    }

    public function find(string $databaseId): Database
    {
        $request = new Request(
            "GET",
            "https://api.notion.com/v1/databases/{$databaseId}",
            [
                "Authorization"  => "Bearer {$this->token}",
                "Notion-Version" => $this->version,
            ]
        );

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

        $request = new Request(
            "POST",
            "https://api.notion.com/v1/databases",
            [
                "Authorization"  => "Bearer {$this->token}",
                "Notion-Version" => $this->version,
                "Content-Type"   => "application/json",
            ],
            json_encode($data),
        );

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
        $request = new Request(
            "PATCH",
            "https://api.notion.com/v1/databases/{$databaseId}",
            [
                "Authorization"  => "Bearer {$this->token}",
                "Notion-Version" => $this->version,
                "Content-Type"   => "application/json",
            ],
            json_encode($data),
        );

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
        $request = new Request(
            "DELETE",
            "https://api.notion.com/v1/blocks/{$databaseId}",
            [
                "Authorization"  => "Bearer {$this->token}",
                "Notion-Version" => $this->version,
            ],
        );

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
