<?php

namespace Notion\Blocks;

use Notion\Blocks\BlockInterface;
use Notion\Configuration;
use Notion\Infrastructure\Http;

class Client
{
    /**
     * @internal Use `\Notion\Notion::blocks()` instead
     */
    public function __construct(
        private readonly Configuration $config,
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

    /** @return BlockInterface[] */
    public function findChildren(string $blockId): array
    {
        $url = "https://api.notion.com/v1/blocks/{$blockId}/children";
        $request = Http::createRequest($url, $this->config);

        /** @var array{ results: list<array{ type: string }> } $body */
        $body = Http::sendRequest($request, $this->config);

        return array_map(
            fn(array $blockArray) => BlockFactory::fromArray($blockArray),
            $body["results"],
        );
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
     *
     * @return BlockInterface[] Newly created blocks
     */
    public function append(string $blockId, array $blocks): array
    {
        $data = json_encode([
            "children" => array_map(fn(BlockInterface $b) => $b->toArray(), $blocks),
        ]);

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
