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
use Notion\Pages\PropertyItems\PropertyItemFactory;
use Notion\Pages\PropertyItems\PropertyItemInterface;
use Notion\Pages\PropertyItems\PropertyItemList;

/**
 * @psalm-import-type PageJson from Page
 */
final readonly class Client
{
    /**
     * @internal Use `\Notion\Notion::pages()` instead
     */
    public function __construct(
        private Configuration $config,
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

    public function findProperty(
        string $pageId,
        string $propertyId,
        string|null $startCursor = null,
        int|null $pageSize = null,
    ): PropertyItemInterface|PropertyItemList {
        $url = "https://api.notion.com/v1/pages/{$pageId}/properties/{$propertyId}";
        $queryParams = [];
        if ($startCursor !== null) {
            $queryParams["start_cursor"] = $startCursor;
        }
        if ($pageSize !== null) {
            $queryParams["page_size"] = (string) $pageSize;
        }
        if (!empty($queryParams)) {
            $url .= "?" . http_build_query($queryParams);
        }

        $request = Http::createRequest($url, $this->config);
        /** @var array<string, mixed> $body */
        $body = Http::sendRequest($request, $this->config);

        return PropertyItemFactory::fromArray($body);
    }

    /**
     * @param list<BlockInterface> $content
     * @param list<string> $filterProperties Property names or IDs to include in
     *                                       the returned page. All properties
     *                                       are returned when empty.
     */
    public function create(Page $page, array $content = [], array $filterProperties = []): Page
    {
        $data = [
            "in_trash" => $page->inTrash,
            "icon" => $page->icon?->toArray(),
            "cover" => $page->cover?->toArray(),
            "properties" => array_map(fn(PropertyInterface $p) => $p->toArray(), $page->properties),
            "parent" => $page->parent->toArray(),
            "children" => array_map(fn(BlockInterface $b) => $b->toArray(), $content),
        ];

        if ($page->parent->isDataSource()) {
            unset($data["parent"]["database_id"]);
        }

        $data = json_encode($data);

        $url = "https://api.notion.com/v1/pages" . self::filterPropertiesQuery($filterProperties);
        $request = Http::createRequest($url, $this->config)
            ->withMethod("POST")
            ->withHeader("Content-Type", "application/json");
        $request->getBody()->write($data);

        /** @psalm-var PageJson $body */
        $body = Http::sendRequest($request, $this->config);

        return Page::fromArray($body);
    }

    /**
     * @param list<string> $filterProperties Property names or IDs to include in
     *                                       the returned page. All properties
     *                                       are returned when empty.
     */
    public function update(Page $page, array $filterProperties = []): Page
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

        $data = [
            "in_trash" => $page->inTrash,
            "icon" => $page->icon?->toArray(),
            "cover" => $page->cover?->toArray(),
            "properties" => array_map(fn(PropertyInterface $p) => $p->toArray(), $updatableProps),
            "parent" => $page->parent->toArray(),
        ];

        $data = json_encode($data);

        $pageId = $page->id;
        $url = "https://api.notion.com/v1/pages/{$pageId}" . self::filterPropertiesQuery($filterProperties);
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
        $archivedPage = $page->delete();

        return $this->update($archivedPage);
    }

    /** @param list<string> $filterProperties */
    private static function filterPropertiesQuery(array $filterProperties): string
    {
        if (empty($filterProperties)) {
            return "";
        }

        $params = array_map(
            fn(string $property) => "filter_properties=" . rawurlencode($property),
            array_values(array_unique($filterProperties)),
        );

        return "?" . implode("&", $params);
    }
}
