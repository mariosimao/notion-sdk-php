<?php

namespace Notion\Comments;

use Notion\Configuration;
use Notion\Infrastructure\Http;
use Notion\Infrastructure\Pagination;

/**
 * @psalm-import-type CommentJson from \Notion\Comments\Comment
 * @psalm-import-type CommentListJson from CommentList
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
     * Retrieve a single page of comments from a page or block
     *
     * @param string $id Page or Block ID
     * @param string|null $startCursor Cursor returned as `nextCursor` by a previous call
     * @param int|null $pageSize Number of comments to return (maximum: 100)
     */
    public function list(string $id, string|null $startCursor = null, int|null $pageSize = null): CommentList
    {
        $url = Pagination::url(
            "https://api.notion.com/v1/comments",
            $startCursor,
            $pageSize,
            [ "block_id" => $id ],
        );
        $request = Http::createRequest($url, $this->config);

        /** @psalm-var CommentListJson $body */
        $body = Http::sendRequest($request, $this->config);

        return CommentList::fromArray($body);
    }

    /**
     * Retrieve all comments from a page or block, following pagination
     *
     * @param string $id Page or Block ID
     *
     * @return list<Comment>
     */
    public function findAll(string $id): array
    {
        $comments = [];
        $startCursor = null;

        do {
            $list = $this->list($id, $startCursor, Pagination::MAX_PAGE_SIZE);
            $comments = [ ...$comments, ...$list->results ];
            $startCursor = $list->nextCursor;
        } while ($list->hasMore && $startCursor !== null);

        return $comments;
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

    // public function find(string $userId): User
    // {
    //     $url = "https://api.notion.com/v1/users/{$userId}";
    //     $request = Http::createRequest($url, $this->config);

    //     /** @psalm-var UserJson $body */
    //     $body = Http::sendRequest($request, $this->config);

    //     return User::fromArray($body);
    // }

    // /**
    //  * @return User[]
    //  */
    // public function findAll(): array
    // {
    //     $url = "https://api.notion.com/v1/users";
    //     $request = Http::createRequest($url, $this->config);

    //     /** @var array{ results: UserJson[] } $body */
    //     $body = Http::sendRequest($request, $this->config);

    //     return array_map(
    //         function (array $userData): User {
    //             return User::fromArray($userData);
    //         },
    //         $body["results"],
    //     );
    // }

    // public function me(): User
    // {
    //     $url = "https://api.notion.com/v1/users/me";
    //     $request = Http::createRequest($url, $this->config);

    //     /** @psalm-var UserJson $body */
    //     $body = Http::sendRequest($request, $this->config);

    //     return User::fromArray($body);
    // }
}
