<?php

namespace Notion\Pages;

use Notion\Blocks\BlockInterface;
use Notion\NotionException;
use Notion\Pages\Properties\PropertyInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;

/**
 * @psalm-import-type PageJson from Page
 */
class Client
{
    private ClientInterface $psrClient;
    private RequestFactoryInterface $requestFactory;
    private string $token;
    private string $version;

    /**
     * @internal Use `\Notion\Notion::pages()` instead
     */
    public function __construct(
        ClientInterface $psrClient,
        RequestFactoryInterface $requestFactory,
        string $token,
        string $version,
    ) {
        $this->psrClient = $psrClient;
        $this->requestFactory = $requestFactory;
        $this->token = $token;
        $this->version = $version;
    }

    public function find(string $pageId): Page
    {
        $url = "https://api.notion.com/v1/pages/{$pageId}";
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

        $url = "https://api.notion.com/v1/pages";
        $request = $this->requestFactory->createRequest("POST", $url)
            ->withHeader("Authorization", "Bearer {$this->token}")
            ->withHeader("Notion-Version", $this->version)
            ->withHeader("Content-Type", "application/json");

        $request->getBody()->write($data);

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
        $url = "https://api.notion.com/v1/pages/{$pageId}";
        $request = $this->requestFactory->createRequest("PATCH", $url)
            ->withHeader("Authorization", "Bearer {$this->token}")
            ->withHeader("Notion-Version", $this->version)
            ->withHeader("Content-Type", "application/json");

        $request->getBody()->write($data);

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

    public function delete(Page $page): Page
    {
        $archivedPage = $page->archive();

        return $this->update($archivedPage);
    }
}
