<?php

namespace Notion\Blocks;

use Notion\Blocks\BlockInterface;
use Notion\Infrastructure\Http;
use Notion\NotionException;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;

class Client
{
    /**
     * @internal Use `\Notion\Notion::blocks()` instead
     */
    public function __construct(
        private readonly ClientInterface $psrClient,
        private readonly RequestFactoryInterface $requestFactory,
        private readonly string $token,
        private readonly string $version,
    ) {
    }

    public function find(string $blockId): BlockInterface
    {
        $url = "https://api.notion.com/v1/blocks/{$blockId}";
        $request = Http::createRequest($this->requestFactory, $this->version, $this->token, $url);

        $response = $this->psrClient->sendRequest($request);

        /** @var array{ type: string } $body */
        $body = Http::parseBody($response);

        return BlockFactory::fromArray($body);
    }

    /** @return BlockInterface[] */
    public function findChildren(string $blockId): array
    {
        $url = "https://api.notion.com/v1/blocks/{$blockId}/children";
        $request = Http::createRequest($this->requestFactory, $this->version, $this->token, $url);

        $response = $this->psrClient->sendRequest($request);

        /** @var array{ results: list<array{ type: string }> } $body */
        $body = Http::parseBody($response);

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
        $request = Http::createRequest($this->requestFactory, $this->version, $this->token, $url)
            ->withMethod("PATCH")
            ->withHeader("Content-Type", "application/json");
        $request->getBody()->write($data);

        $response = $this->psrClient->sendRequest($request);

        /** @var array{ results: list<array{ type: string }> } $body */
        $body = Http::parseBody($response);

        return array_map(
            fn(array $blockArray): BlockInterface => BlockFactory::fromArray($blockArray),
            $body["results"],
        );
    }

    public function update(BlockInterface $block): BlockInterface
    {
        $blockId = $block->metadata()->id;

        $json = json_encode($block->toUpdateArray());

        $url = "https://api.notion.com/v1/blocks/{$blockId}";
        $request = Http::createRequest($this->requestFactory, $this->version, $this->token, $url)
            ->withMethod("PATCH")
            ->withHeader("Content-Type", "application/json");

        $request->getBody()->write($json);

        $response = $this->psrClient->sendRequest($request);

        /** @var array{ type: string } $body */
        $body = Http::parseBody($response);

        return BlockFactory::fromArray($body);
    }

    public function delete(string $blockId): BlockInterface
    {
        $url = "https://api.notion.com/v1/blocks/{$blockId}";
        $request = Http::createRequest($this->requestFactory, $this->version, $this->token, $url)
            ->withMethod("DELETE");

        $response = $this->psrClient->sendRequest($request);

        /** @var array{ type: string } $body */
        $body = Http::parseBody($response);

        return BlockFactory::fromArray($body);
    }
}
