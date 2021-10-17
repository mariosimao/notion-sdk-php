<?php

namespace Notion\Pages;

use Notion\NotionException;
use Nyholm\Psr7\Request;
use Psr\Http\Client\ClientInterface;

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

    public function find(string $pageId): Page
    {
        $request = new Request(
            "GET",
            "https://api.notion.com/v1/pages/{$pageId}",
            [
                "Authorization"  => "Bearer {$this->token}",
                "Notion-Version" => $this->version,
            ]
        );

        $response = $this->psrClient->sendRequest($request);
        $body = json_decode((string) $response->getBody(), true);

        if ($response->getStatusCode() !== 200) {
            $message = $body["message"];
            $code = $body["code"];

            throw new NotionException($message, $code);
        }

        return Page::fromArray($body);
    }
}
