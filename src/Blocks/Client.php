<?php

namespace Notion\Blocks;

use Notion\Blocks\BlockInterface;
use Notion\Configuration;
use Notion\Infrastructure\Http;
use Notion\Infrastructure\Pagination;

/** @psalm-import-type BlockListJson from BlockList */
final readonly class Client
{
    /**
     * @internal Use `\Notion\Notion::blocks()` instead
     */
    public function __construct(
        private Configuration $config,
    ) {
    }

    public function find(string $blockId): BlockInterface
    {
        $url = "https://api.notion.com/v1/blocks/{$blockId}";
        $request = Http::createRequest($url, $this->config);

        /** @var array{ type: string } $body */
        $body = Http::sendRequest($request, $this->config);

        return BlockFactory::fromArray($body);
    }

    /**
     * Retrieve a single page of a block's children.
     *
     * @param string|null $startCursor Cursor returned as `nextCursor` by a previous call
     * @param int|null $pageSize Number of children to return (maximum: 100)
     */
    public function listChildren(
        string $blockId,
        string|null $startCursor = null,
        int|null $pageSize = null,
    ): BlockList {
        $url = Pagination::url(
            "https://api.notion.com/v1/blocks/{$blockId}/children",
            $startCursor,
            $pageSize,
        );
        $request = Http::createRequest($url, $this->config);

        /** @psalm-var BlockListJson $body */
        $body = Http::sendRequest($request, $this->config);

        return BlockList::fromArray($body);
    }

    /**
     * Retrieve all children of a block, following pagination.
     *
     * @return list<BlockInterface>
     */
    public function findChildren(string $blockId): array
    {
        $children = [];
        $startCursor = null;

        do {
            $list = $this->listChildren($blockId, $startCursor, Pagination::MAX_PAGE_SIZE);
            $children = [ ...$children, ...$list->results ];
            $startCursor = $list->nextCursor;
        } while ($list->hasMore && $startCursor !== null);

        return $children;
    }

    /** @return BlockInterface[] */
    public function findChildrenRecursive(string $blockId): array
    {
        $children = $this->findChildren($blockId);
        return array_map(
            function (BlockInterface $block) {
                if ($block->metadata()->hasChildren) {
                    $blockChildren = $this->findChildrenRecursive($block->metadata()->id);
                    return $block->changeChildren(...$blockChildren);
                }

                return $block;
            },
            $children
        );
    }

    /**
     * @param BlockInterface[] $blocks
     * @param string|null $after ID of an existing child block after which the new blocks are inserted
     *
     * @return BlockInterface[] Newly created blocks
     */
    public function append(string $blockId, array $blocks, string|null $after = null): array
    {
        $payload = [
            "children" => array_map(fn(BlockInterface $b) => $b->toArray(), $blocks),
        ];
        if ($after !== null) {
            $payload["after"] = $after;
        }
        $data = json_encode($payload);

        $url = "https://api.notion.com/v1/blocks/{$blockId}/children";
        $request = Http::createRequest($url, $this->config)
            ->withMethod("PATCH")
            ->withHeader("Content-Type", "application/json");
        $request->getBody()->write($data);

        /** @var array{ results: list<array{ type: string }> } $body */
        $body = Http::sendRequest($request, $this->config);

        return array_map(
            fn(array $blockArray): BlockInterface => BlockFactory::fromArray($blockArray),
            $body["results"],
        );
    }

    public function update(BlockInterface $block): BlockInterface
    {
        $blockId = $block->metadata()->id;
        $blockType = $block->metadata()->type->value;

        $data = $block->toArray();

        unset($data["type"]);
        unset($data["id"]);
        unset($data["created_time"]);
        unset($data["last_edited_time"]);
        unset($data["created_by"]);
        unset($data["last_edited_by"]);
        unset($data["has_children"]);
        if (is_array($data[$blockType])) {
            unset($data[$blockType]["children"]);
        }

        $json = json_encode($data);

        $url = "https://api.notion.com/v1/blocks/{$blockId}";
        $request = Http::createRequest($url, $this->config)
            ->withMethod("PATCH")
            ->withHeader("Content-Type", "application/json");

        $request->getBody()->write($json);

        /** @var array{ type: string } $body */
        $body = Http::sendRequest($request, $this->config);

        return BlockFactory::fromArray($body);
    }

    public function delete(string $blockId): BlockInterface
    {
        $url = "https://api.notion.com/v1/blocks/{$blockId}";
        $request = Http::createRequest($url, $this->config)
            ->withMethod("DELETE");

        /** @var array{ type: string } $body */
        $body = Http::sendRequest($request, $this->config);

        return BlockFactory::fromArray($body);
    }
}
