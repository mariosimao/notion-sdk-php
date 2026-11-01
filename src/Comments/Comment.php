<?php

namespace Notion\Comments;

use DateTimeImmutable;
use Notion\Common\Date;
use Notion\Common\ParentBlock;
use Notion\Common\RichText;

/**
 * @psalm-import-type ParentJson from \Notion\Common\ParentBlock
 * @psalm-import-type RichTextJson from \Notion\Common\RichText
 * @psalm-import-type CommentAttachmentJson from \Notion\Comments\CommentAttachment
 * @psalm-import-type CommentDisplayNameJson from \Notion\Comments\CommentDisplayName
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
 *      rich_text: RichTextJson[],
 *      attachments?: CommentAttachmentJson[],
 *      display_name?: CommentDisplayNameJson|null,
 *      has_pending_attachments?: bool
 * }
 */
final readonly class Comment
{
    /**
     * @param RichText[] $text
     * @param CommentAttachment[] $attachments
     */
    private function __construct(
        public string $id,
        public ParentBlock|null $parent,
        public string|null $discussionId,
        public DateTimeImmutable $createdTime,
        public DateTimeImmutable $lastEditedTime,
        public string $userId,
        public array $text,
        public array $attachments = [],
        public CommentDisplayName|null $displayName = null,
        public bool $hasPendingAttachments = false,
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
        $attachments = array_map(fn ($a) => CommentAttachment::fromArray($a), $array["attachments"] ?? []);
        $displayName = isset($array["display_name"]) ? CommentDisplayName::fromArray($array["display_name"]) : null;
        $hasPendingAttachments = $array["has_pending_attachments"] ?? false;

        return new self(
            $id,
            $parent,
            $discussionId,
            $createdTime,
            $lastEditedTime,
            $userId,
            $text,
            $attachments,
            $displayName,
            $hasPendingAttachments,
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
            "rich_text" => array_map(fn (RichText $t) => $t->toArray(), $this->text),
            "attachments" => array_map(fn (CommentAttachment $a) => $a->toArray(), $this->attachments),
            "has_pending_attachments" => $this->hasPendingAttachments,
        ];

        if ($this->parent !== null) {
            $array["parent"] = $this->parent->toArray();
        }

        if ($this->discussionId !== null) {
            $array["discussion_id"] = $this->discussionId;
        }

        if ($this->displayName !== null) {
            $array["display_name"] = $this->displayName->toArray();
        }

        return $array;
    }

    /** Add attachments to a comment. Comments support up to 3 attachments. */
    public function addAttachment(CommentAttachment ...$attachments): self
    {
        return $this->changeAttachments(...[...$this->attachments, ...$attachments]);
    }

    public function changeAttachments(CommentAttachment ...$attachments): self
    {
        return new self(
            $this->id,
            $this->parent,
            $this->discussionId,
            $this->createdTime,
            $this->lastEditedTime,
            $this->userId,
            $this->text,
            $attachments,
            $this->displayName,
            $this->hasPendingAttachments,
        );
    }

    public function changeDisplayName(CommentDisplayName $displayName): self
    {
        return new self(
            $this->id,
            $this->parent,
            $this->discussionId,
            $this->createdTime,
            $this->lastEditedTime,
            $this->userId,
            $this->text,
            $this->attachments,
            $displayName,
            $this->hasPendingAttachments,
        );
    }

    public function removeDisplayName(): self
    {
        return new self(
            $this->id,
            $this->parent,
            $this->discussionId,
            $this->createdTime,
            $this->lastEditedTime,
            $this->userId,
            $this->text,
            $this->attachments,
            null,
            $this->hasPendingAttachments,
        );
    }
}
