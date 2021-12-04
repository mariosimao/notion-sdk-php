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

    /** @return list<BlockInterface> */
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

    /** @return list<BlockInterface> */
    public function findChildrenRecursive(string $blockId): array
    {
        $children = $this->findChildren($blockId);
        return array_map(
            function (BlockInterface $block) {
                if ($block->block()->hasChildren()) {
                    $blockChildren = $this->findChildrenRecursive($block->block()->id());
                    return $block->changeChildren($blockChildren);
                }

                return $block;
            },
            $children
        );
    }
}
