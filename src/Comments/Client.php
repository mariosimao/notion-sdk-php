<?php

namespace Notion\Comments;

use Notion\Configuration;
use Notion\Infrastructure\Http;

/**
 * @psalm-import-type CommentJson from \Notion\Comments\Comment
 */
class Client
{
    /**
     * @internal Use `\Notion\Notion::comments()` instead
     */
    public function __construct(
        private readonly Configuration $config,
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
