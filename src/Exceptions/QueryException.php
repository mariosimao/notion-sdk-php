<?php

namespace Notion\Exceptions;

final class QueryException extends NotionException
{
    public static function topLevelOrFilterCannotBeWindowed(): self
    {
        return new self(
            "Queries with a top-level \"or\" filter cannot be windowed past the per-query result limit. " .
            "Run one query per \"or\" branch and merge the results, or rewrite the filter as an \"and\" group.",
        );
    }

    public static function windowCannotAdvance(string|null $createdTime): self
    {
        $createdTime ??= "null";

        return new self(
            "Cannot read past the per-query result limit: the created_time window could not advance " .
            "past {$createdTime}. More rows share this created_time than the limit allows. " .
            "Add a filter to narrow the query.",
        );
    }
}
