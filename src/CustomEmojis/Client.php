<?php

namespace Notion\CustomEmojis;

use Notion\Configuration;
use Notion\Infrastructure\Http;

/** @psalm-import-type ResultJson from Result */
final readonly class Client
{
    /**
     * @internal Use `\Notion\Notion::customEmojis()` instead
     */
    public function __construct(
        private Configuration $config,
    ) {
    }

    /**
     * List a single page of custom emojis in the workspace
     */
    public function list(Query|null $query = null): Result
    {
        $query ??= Query::all();

        $url = "https://api.notion.com/v1/custom_emojis";
        $queryString = http_build_query($query->toArray());
        if ($queryString !== "") {
            $url .= "?{$queryString}";
        }

        $request = Http::createRequest($url, $this->config);

        /** @psalm-var ResultJson $body */
        $body = Http::sendRequest($request, $this->config);

        return Result::fromArray($body);
    }

    /**
     * List all custom emojis in the workspace, following pagination
     *
     * @param string|null $name Filter by exact name match
     *
     * @return CustomEmoji[]
     */
    public function findAll(string|null $name = null): array
    {
        $query = Query::all()->changePageSize(Query::MAX_PAGE_SIZE);
        if ($name !== null) {
            $query = $query->changeName($name);
        }

        $emojis = [];
        do {
            $result = $this->list($query);
            $emojis = [...$emojis, ...$result->results];

            if ($result->nextCursor !== null) {
                $query = $query->changeStartCursor($result->nextCursor);
            }
        } while ($result->hasMore && $result->nextCursor !== null);

        return $emojis;
    }

    /**
     * Find a custom emoji by its exact name
     */
    public function findByName(string $name): CustomEmoji|null
    {
        return $this->list(Query::name($name))->results[0] ?? null;
    }
}
