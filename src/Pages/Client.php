<?php

namespace Notion\Pages;

use Notion\Blocks\BlockInterface;
use Notion\Infrastructure\Http;
use Notion\Pages\Properties\PropertyInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;

/**
 * @psalm-import-type PageJson from Page
 */
class Client
{
    /**
     * @internal Use `\Notion\Notion::pages()` instead
     */
    public function __construct(
        private readonly ClientInterface $psrClient,
        private readonly RequestFactoryInterface $requestFactory,
        private readonly string $token,
        private readonly string $version,
    ) {}

    public function find(string $pageId): Page
    {
        $url = "https://api.notion.com/v1/pages/{$pageId}";
        $request = Http::createRequest($this->requestFactory, $this->version, $this->token, $url);

        $response = $this->psrClient->sendRequest($request);

        /** @psalm-var PageJson $body */
        $body = Http::parseBody($response);

        return Page::fromArray($body);
    }

    /** @param list<BlockInterface> $content */
    public function create(Page $page, array $content = []): Page
    {
        $data = json_encode([
            "archived" => $page->archived,
            "icon" => $page->icon?->toArray(),
            "cover" => $page->cover?->toArray(),
            "properties" => array_map(fn(PropertyInterface $p) => $p->toArray(), $page->properties),
            "parent" => $page->parent->toArray(),
            "children" => array_map(fn(BlockInterface $b) => $b->toArray(), $content),
        ], JSON_PRETTY_PRINT);


        $url = "https://api.notion.com/v1/pages";
        $request = Http::createRequest($this->requestFactory, $this->version, $this->token, $url)
            ->withMethod("POST")
            ->withHeader("Content-Type", "application/json");
        $request->getBody()->write($data);

        $response = $this->psrClient->sendRequest($request);

        /** @psalm-var PageJson $body */
        $body = Http::parseBody($response);

        return Page::fromArray($body);
    }

    public function update(Page $page): Page
    {
        $data = json_encode([
            "archived" => $page->archived,
            "icon" => $page->icon?->toArray(),
            "cover" => $page->cover?->toArray(),
            "properties" => array_map(fn(PropertyInterface $p) => $p->toArray(), $page->properties),
            "parent" => $page->parent->toArray(),
        ]);

        $pageId = $page->id;
        $url = "https://api.notion.com/v1/pages/{$pageId}";
        $request = Http::createRequest($this->requestFactory, $this->version, $this->token, $url)
            ->withMethod("PATCH")
            ->withHeader("Content-Type", "application/json");
        $request->getBody()->write($data);

        $response = $this->psrClient->sendRequest($request);

        /** @psalm-var PageJson $body */
        $body = Http::parseBody($response);

        return Page::fromArray($body);
    }

    public function delete(Page $page): Page
    {
        $archivedPage = $page->archive();

        return $this->update($archivedPage);
    }
}
