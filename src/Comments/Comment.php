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
class Comment
{
    /** @param RichText[] $text */
    private function __construct(
        public readonly string $id,
        public readonly ParentBlock|null $parent,
        public readonly string|null $discussionId,
        public readonly DateTimeImmutable $createdTime,
        public readonly DateTimeImmutable $lastEditedTime,
        public readonly string $userId,
        public readonly array $text,
    ) {
    }

    public static function create(string $pageId, RichText ...$text): self
    {
        $createdTime = $lastEditedTime = new DateTimeImmutable("now");

        $id = $userId = "";
        $parent = ParentBlock::page($pageId);
        $discussionId = null;

        return new self($id, $parent, $discussionId, $createdTime, $lastEditedTime, $userId, $text);
    }

    public static function createReply(string $discussionId, RichText ...$text): self
    {
        $createdTime = $lastEditedTime = new DateTimeImmutable("now");

        $id = $userId = "";
        $parent = null;

        return new self($id, $parent, $discussionId, $createdTime, $lastEditedTime, $userId, $text);
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

        return new self($id, $parent, $discussionId, $createdTime, $lastEditedTime, $userId, $text);
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
            "rich_text" => array_map(fn (RichText $t) => $t->toArray(), $this->text),
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
