<?php

namespace Notion\Comments;

use DateTimeImmutable;
use Notion\Common\Date;
use Notion\Common\ParentBlock;
use Notion\Common\RichText;

/**
 * @psalm-import-type ParentJson from \Notion\Common\ParentBlock
 * @psalm-import-type RichTextJson from \Notion\Common\RichText
 *
 * @psalm-type CommentJson = array{
 *      id: string,
 *      parent?: ParentJson,
 *      discussion_id?: string,
 *      created_time: string,
 *      last_edited_time: string,
 *      created_by: array{
 *          object: "user",
 *          id: string
 *      },
 *      rich_text: RichTextJson[]
 * }
 */
final readonly class Comment
{
    private function __construct(
        public string $id,
        public ParentBlock|null $parent,
        public string|null $discussionId,
        public DateTimeImmutable $createdTime,
        public DateTimeImmutable $lastEditedTime,
        public string $userId,
        public CommentBody $body,
    ) {
    }

    /**
     * Create a new comment on a page
     *
     * Use `CommentBody::richText()` or `CommentBody::markdown()` to define the
     * comment content.
     */
    public static function create(string $pageId, CommentBody $body): self
    {
        $createdTime = $lastEditedTime = new DateTimeImmutable("now");

        $id = $userId = "";
        $parent = ParentBlock::page($pageId);
        $discussionId = null;

        return new self($id, $parent, $discussionId, $createdTime, $lastEditedTime, $userId, $body);
    }

    /**
     * Create a reply on an existing discussion thread
     */
    public static function createReply(string $discussionId, CommentBody $body): self
    {
        $createdTime = $lastEditedTime = new DateTimeImmutable("now");

        $id = $userId = "";
        $parent = null;

        return new self($id, $parent, $discussionId, $createdTime, $lastEditedTime, $userId, $body);
    }

    /** @psalm-param CommentJson $array */
    public static function fromArray(array $array): self
    {
        $id = $array["id"];
        $parent = !empty($array["parent"]) ? ParentBlock::fromArray($array["parent"]) : null;
        $discussionId = $array["discussion_id"] ?? null;
        $createdTime = new DateTimeImmutable($array["created_time"]);
        $lastEditedTime = new DateTimeImmutable($array["last_edited_time"]);
        $userId = $array["created_by"]["id"];
        $text = array_map(fn ($t) => RichText::fromArray($t), $array["rich_text"]);
        $body = CommentBody::richText(...$text);

        return new self($id, $parent, $discussionId, $createdTime, $lastEditedTime, $userId, $body);
    }

    public function changeBody(CommentBody $body): self
    {
        return new self(
            $this->id,
            $this->parent,
            $this->discussionId,
            $this->createdTime,
            $this->lastEditedTime,
            $this->userId,
            $body,
        );
    }

    public function toArray(): array
    {
        $array = [
            "id" => $this->id,
            "created_time" => $this->createdTime->format(Date::FORMAT),
            "last_edited_time" => $this->lastEditedTime->format(Date::FORMAT),
            "created_by" => [
                "object" => "user",
                "id" => $this->userId,
            ],
            ...$this->body->toArray(),
        ];

        if ($this->parent !== null) {
            $array["parent"] = $this->parent->toArray();
        }

        if ($this->discussionId !== null) {
            $array["discussion_id"] = $this->discussionId;
        }

        return $array;
    }
}
