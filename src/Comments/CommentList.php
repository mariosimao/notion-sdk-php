<?php

namespace Notion\Comments;

/**
 * A single page of comments returned by the Notion API.
 *
 * @psalm-import-type CommentJson from Comment
 *
 * @psalm-type CommentListJson = array{
 *      results: list<CommentJson>,
 *      next_cursor: string|null,
 *      has_more: bool,
 *      ...
 * }
 *
 * @psalm-immutable
 */
final readonly class CommentList
{
    /** @param list<Comment> $results */
    private function __construct(
        public array $results,
        public string|null $nextCursor,
        public bool $hasMore,
    ) {
    }

    /** @param list<Comment> $results */
    public static function create(array $results, string|null $nextCursor = null, bool $hasMore = false): self
    {
        return new self($results, $nextCursor, $hasMore);
    }

    /**
     * @psalm-param CommentListJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        $results = array_map(
            fn(array $comment): Comment => Comment::fromArray($comment),
            $array["results"],
        );

        return new self($results, $array["next_cursor"], $array["has_more"]);
    }
}
