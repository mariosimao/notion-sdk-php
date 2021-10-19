<?php

namespace Notion\Pages;

use Notion\Blocks\BlockInterface;
use Notion\NotionException;
use Notion\Pages\Properties\PropertyInterface;
use Nyholm\Psr7\Request;
use Psr\Http\Client\ClientInterface;

/**
 * @psalm-import-type PageJson from Page
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

        /** @var array */
        $body = json_decode((string) $response->getBody(), true);

        if ($response->getStatusCode() !== 200) {
            /** @var array{ message: string, code: string} $body */
            $message = $body["message"];
            $code = $body["code"];

            throw new NotionException($message, $code);
        }

        /** @psalm-var PageJson $body */
        return Page::fromArray($body);
    }

    public function create(Page $page, BlockInterface ...$content): Page
    {
        $data = json_encode([
            "archived" => $page->archived(),
            "icon" => $page->icon()?->toArray(),
            "cover" => $page->cover()?->toArray(),
            "properties" => array_map(fn(PropertyInterface $p) => $p->toArray(), $page->properties()),
            "parent" => $page->parent()->toArray(),
            "children" => array_map(fn(BlockInterface $b) => $b->toArray(), $content),
        ]);

        $request = new Request(
            "POST",
            "https://api.notion.com/v1/pages",
            [
                "Authorization"  => "Bearer {$this->token}",
                "Notion-Version" => $this->version,
                "Content-Type"   => "application/json",
            ],
            $data,
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

        /** @psalm-var PageJson $body */
        return Page::fromArray($body);
    }

    public function update(Page $page): Page
    {
        $data = json_encode([
            "archived" => $page->archived(),
            "icon" => $page->icon()?->toArray(),
            "cover" => $page->cover()?->toArray(),
            "properties" => array_map(fn(PropertyInterface $p) => $p->toArray(), $page->properties()),
            "parent" => $page->parent()->toArray(),
        ]);

        $pageId = $page->id();
        $request = new Request(
            "PATCH",
            "https://api.notion.com/v1/pages/{$pageId}",
            [
                "Authorization"  => "Bearer {$this->token}",
                "Notion-Version" => $this->version,
                "Content-Type"   => "application/json",
            ],
            $data,
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

        /** @psalm-var PageJson $body */
        return Page::fromArray($body);
    }
}
