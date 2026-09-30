<?php

namespace Notion\Pages;

use Notion\Blocks\BlockInterface;
use Notion\Configuration;
use Notion\Infrastructure\Http;
use Notion\Pages\Markdown\MarkdownUpdateInterface;
use Notion\Pages\Markdown\PageMarkdown;
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
 * @psalm-import-type PageMarkdownJson from PageMarkdown
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

    /** @param list<BlockInterface> $content */
    public function create(Page $page, array $content = []): Page
    {
        return $this->sendCreate($page, [
            "children" => array_map(fn(BlockInterface $b) => $b->toArray(), $content),
        ]);
    }

    /** @param string $markdown Page content in Notion enhanced markdown. */
    public function createFromMarkdown(Page $page, string $markdown): Page
    {
        return $this->sendCreate($page, [ "markdown" => $markdown ]);
    }

    public function findMarkdown(string $pageId, bool $includeTranscript = false): PageMarkdown
    {
        $url = "https://api.notion.com/v1/pages/{$pageId}/markdown";
        if ($includeTranscript) {
            $url .= "?include_transcript=true";
        }

        $request = Http::createRequest($url, $this->config);

        /** @psalm-var PageMarkdownJson $body */
        $body = Http::sendRequest($request, $this->config);

        return PageMarkdown::fromArray($body);
    }

    public function updateMarkdown(string $pageId, MarkdownUpdateInterface $update): PageMarkdown
    {
        $url = "https://api.notion.com/v1/pages/{$pageId}/markdown";
        $request = Http::createRequest($url, $this->config)
            ->withMethod("PATCH")
            ->withHeader("Content-Type", "application/json");
        $request->getBody()->write(json_encode($update->toArray()));

        /** @psalm-var PageMarkdownJson $body */
        $body = Http::sendRequest($request, $this->config);

        return PageMarkdown::fromArray($body);
    }

    /** @param array<string, mixed> $content */
    private function sendCreate(Page $page, array $content): Page
    {
        $parent = $page->parent->toArray();
        if ($page->parent->isDataSource()) {
            unset($parent["database_id"]);
        }

        $data = [
            "in_trash" => $page->inTrash,
            "icon" => $page->icon?->toArray(),
            "cover" => $page->cover?->toArray(),
            "properties" => array_map(fn(PropertyInterface $p) => $p->toArray(), $page->properties),
            "parent" => $parent,
            ...$content,
        ];

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

        $data = [
            "in_trash" => $page->inTrash,
            "icon" => $page->icon?->toArray(),
            "cover" => $page->cover?->toArray(),
            "properties" => array_map(fn(PropertyInterface $p) => $p->toArray(), $updatableProps),
            "parent" => $page->parent->toArray(),
        ];

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
