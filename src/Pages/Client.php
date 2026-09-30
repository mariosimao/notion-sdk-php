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
     * Content cannot be combined with a template other than `PageTemplate::none()`.
     *
     * @param list<BlockInterface> $content
     */
    public function create(Page $page, array $content = [], PageTemplate|null $template = null): Page
    {
        $data = [
            "in_trash" => $page->inTrash,
            "icon" => $page->icon?->toArray(),
            "cover" => $page->cover?->toArray(),
            "properties" => array_map(fn(PropertyInterface $p) => $p->toArray(), $page->properties),
            "parent" => $page->parent->toArray(),
        ];

        if (!empty($content)) {
            $data["children"] = array_map(fn(BlockInterface $b) => $b->toArray(), $content);
        }

        if ($template !== null) {
            $data["template"] = $template->toArray();
        }

        if ($page->parent->isDataSource()) {
            unset($data["parent"]["database_id"]);
        }

        $data = json_encode($data);

        $url = "https://api.notion.com/v1/pages";
        $request = Http::createRequest($url, $this->config)
            ->withMethod("POST")
            ->withHeader("Content-Type", "application/json");
        $request->getBody()->write($data);

        /** @psalm-var PageJson $body */
        $body = Http::sendRequest($request, $this->config);

        return Page::fromArray($body);
    }

    /**
     * @param PageTemplate|null $template Template merged into the page. `PageTemplate::none()` is not allowed.
     * @param bool $eraseContent Irreversibly delete all page content before applying the template (if any).
     */
    public function update(Page $page, PageTemplate|null $template = null, bool $eraseContent = false): Page
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

        if ($template !== null) {
            $data["template"] = $template->toArray();
        }

        if ($eraseContent) {
            $data["erase_content"] = true;
        }

        $data = json_encode($data);

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
        $archivedPage = $page->delete();

        return $this->update($archivedPage);
    }
}
