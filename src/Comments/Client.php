<?php

namespace Notion\Comments;

use Notion\Configuration;
use Notion\Infrastructure\Http;

/**
 * @psalm-import-type CommentJson from \Notion\Comments\Comment
 */
final readonly class Client
{
    /**
     * @internal Use `\Notion\Notion::comments()` instead
     */
    public function __construct(
        private Configuration $config,
    ) {
    }

    /**
     * List comments from a page
     *
     * @param string $id Page or Block ID
     *
     * @return Comment[]
     */
    public function list(string $id): array
    {
        $url = "https://api.notion.com/v1/comments?block_id={$id}";
        $request = Http::createRequest($url, $this->config);

        /** @psalm-var array{ results: CommentJson[] } $body */
        $body = Http::sendRequest($request, $this->config);

        return array_map(fn($c) => Comment::fromArray($c), $body["results"]);
    }

    public function create(Comment $comment): Comment
    {
        $data = $comment->toArray();
        unset($data["id"]);
        unset($data["created_time"]);
        unset($data["last_edited_time"]);
        unset($data["created_by"]);

        $json = json_encode($data);

        $url = "https://api.notion.com/v1/comments";
        $request = Http::createRequest($url, $this->config)
            ->withMethod("POST")
            ->withHeader("Content-Type", "application/json");
        $request->getBody()->write($json);

        /** @psalm-var CommentJson $body */
        $body = Http::sendRequest($request, $this->config);

        return Comment::fromArray($body);
    }

    /**
     * Update the content of a comment
     *
     * Only comments created by the integration can be updated.
     */
    public function update(Comment $comment): Comment
    {
        $data = [
            "rich_text" => $comment->toArray()["rich_text"],
        ];

        $json = json_encode($data);

        $url = "https://api.notion.com/v1/comments/{$comment->id}";
        $request = Http::createRequest($url, $this->config)
            ->withMethod("PATCH")
            ->withHeader("Content-Type", "application/json");
        $request->getBody()->write($json);

        /** @psalm-var CommentJson $body */
        $body = Http::sendRequest($request, $this->config);

        return Comment::fromArray($body);
    }

    /**
     * Delete a comment
     *
     * Only comments created by the integration can be deleted.
     *
     * @param string $commentId Comment ID
     */
    public function delete(string $commentId): Comment
    {
        $url = "https://api.notion.com/v1/comments/{$commentId}";
        $request = Http::createRequest($url, $this->config)
            ->withMethod("DELETE");

        /** @psalm-var CommentJson $body */
        $body = Http::sendRequest($request, $this->config);

        return Comment::fromArray($body);
    }
}
