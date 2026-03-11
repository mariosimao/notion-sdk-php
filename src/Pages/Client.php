<?php

namespace Notion\Pages;

use Notion\Blocks\BlockInterface;
use Notion\Configuration;
use Notion\Infrastructure\Http;
use Notion\Pages\Properties\CreatedBy;
use Notion\Pages\Properties\CreatedTime;
use Notion\Pages\Properties\Formula;
use Notion\Pages\Properties\LastEditedBy;
use Notion\Pages\Properties\LastEditedTime;
use Notion\Pages\Properties\PropertyInterface;
use Notion\Pages\Properties\PropertyType;
use Notion\Pages\Properties\UniqueId;

/**
 * @psalm-import-type PageJson from Page
 */
class Client
{
    /**
     * @internal Use `\Notion\Notion::pages()` instead
     */
    public function __construct(
        private readonly Configuration $config,
    ) {
    }

    public function find(string $pageId): Page
    {
        $url = "https://api.notion.com/v1/pages/{$pageId}";
        $request = Http::createRequest($url, $this->config);

        /** @psalm-var PageJson $body */
        $body = Http::sendRequest($request, $this->config);

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
        ]);

        $url = "https://api.notion.com/v1/pages";
        $request = Http::createRequest($url, $this->config)
            ->withMethod("POST")
            ->withHeader("Content-Type", "application/json");
        $request->getBody()->write($data);

        /** @psalm-var PageJson $body */
        $body = Http::sendRequest($request, $this->config);

        return Page::fromArray($body);
    }

    public function update(Page $page): Page
    {
        $notUpdatableProps = [
            PropertyType::CreatedBy,
            PropertyType::CreatedTime,
            PropertyType::Formula,
            PropertyType::LastEditedBy,
            PropertyType::LastEditedTime,
            PropertyType::Rollup,
            PropertyType::UniqueId,
        ];
        $updatableProps = array_filter(
            $page->properties,
            function (PropertyInterface $p) use ($notUpdatableProps) {
                return (!in_array($p->metadata()->type, $notUpdatableProps));
            }
        );

        $data = json_encode([
            "archived" => $page->archived,
            "icon" => $page->icon?->toArray(),
            "cover" => $page->cover?->toArray(),
            "properties" => array_map(fn(PropertyInterface $p) => $p->toArray(), $updatableProps),
            "parent" => $page->parent->toArray(),
        ]);

        $pageId = $page->id;
        $url = "https://api.notion.com/v1/pages/{$pageId}";
        $request = Http::createRequest($url, $this->config)
            ->withMethod("PATCH")
            ->withHeader("Content-Type", "application/json");
        $request->getBody()->write($data);

        /** @psalm-var PageJson $body */
        $body = Http::sendRequest($request, $this->config);

        return Page::fromArray($body);
    }

    public function delete(Page $page): Page
    {
        $archivedPage = $page->archive();

        return $this->update($archivedPage);
    }
}
