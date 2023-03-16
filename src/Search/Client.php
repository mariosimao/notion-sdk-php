<?php

namespace Notion\Search;

use Notion\Configuration;
use Notion\Infrastructure\Http;

/** @psalm-import-type ResultJson from \Notion\Search\Result */
class Client
{
    /**
     * @internal Use `\Notion\Notion::search()` instead
     */
    public function __construct(
        private readonly Configuration $config,
    ) {
    }

    public function search(Query $query): Result
    {
        $data = json_encode($query->toArray(), JSON_PRETTY_PRINT);
        $url = "https://api.notion.com/v1/search";
        $request = Http::createRequest($url, $this->config)
            ->withMethod("POST")
            ->withHeader("Content-Type", "application/json");
        $request->getBody()->write($data);

        /** @psalm-var ResultJson $body */
        $body = Http::sendRequest($request, $this->config);

        return Result::fromArray($body);
    }
}