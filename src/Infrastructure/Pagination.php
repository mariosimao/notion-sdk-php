<?php

namespace Notion\Infrastructure;

/** @internal */
final readonly class Pagination
{
    public const MAX_PAGE_SIZE = 100;

    /**
     * @param array<string, string|int> $params Additional query parameters
     */
    public static function url(
        string $url,
        string|null $startCursor,
        int|null $pageSize,
        array $params = [],
    ): string {
        if ($startCursor !== null) {
            $params["start_cursor"] = $startCursor;
        }
        if ($pageSize !== null) {
            $params["page_size"] = $pageSize;
        }

        if ($params === []) {
            return $url;
        }

        return $url . "?" . http_build_query($params);
    }
}
