<?php

namespace Notion\Blocks;

use Notion\Blocks\BlockInterface;
use Notion\NotionException;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;

class Client
{
    private ClientInterface $psrClient;
    private RequestFactoryInterface $requestFactory;
    private string $token;
    private string $version;

    /**
     * @internal Use `\Notion\Notion::blocks()` instead
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

    public function find(string $blockId): BlockInterface
    {
        $url = "https://api.notion.com/v1/blocks/{$blockId}";
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

        /** @var array{ type: string } $body */
        return BlockFactory::fromArray($body);
    }

    /** @return BlockInterface[] */
    public function findChildren(string $blockId): array
    {
        $url = "https://api.notion.com/v1/blocks/{$blockId}/children";
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

        /** @var array{ results: list<array{ type: string }> } $body */
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
    public function add(string $blockId, array $blocks): array
    {
        $data = json_encode([
            "children" => array_map(fn(BlockInterface $b) => $b->toArray(), $blocks),
        ]);

        $url = "https://api.notion.com/v1/blocks/{$blockId}/children";
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

        /** @var array{ results: list<array{ type: string }> } $body */
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
        $request = $this->requestFactory->createRequest("PATCH", $url)
            ->withHeader("Authorization", "Bearer {$this->token}")
            ->withHeader("Notion-Version", $this->version)
            ->withHeader("Content-Type", "application/json");

        $request->getBody()->write($json);

        $response = $this->psrClient->sendRequest($request);

        /** @var array */
        $body = json_decode((string) $response->getBody(), true);

        if ($response->getStatusCode() !== 200) {
            /** @var array{ message: string, code: string} $body */
            $message = $body["message"];
            $code = $body["code"];

            throw new NotionException($message, $code);
        }

        /** @var array{ type: string } $body */
        return BlockFactory::fromArray($body);
    }

    public function delete(string $blockId): BlockInterface
    {
        $url = "https://api.notion.com/v1/blocks/{$blockId}";
        $request = $this->requestFactory->createRequest("DELETE", $url)
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

        /** @var array{ type: string } $body */
        return BlockFactory::fromArray($body);
    }
}
