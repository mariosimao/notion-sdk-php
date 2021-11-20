<?php

namespace Notion\Blocks;

use DateTimeImmutable;
use Notion\Common\Date;

/**
 * @psalm-type BlockJson = array{
 *      type: string,
 *      id: string,
 *      created_time: string,
 *      last_edited_time: string,
 *      archived: bool,
 *      has_children: bool,
 * }
 *
 * @psalm-immutable
 */
class Block
{
    public const TYPE_PARAGRAPH = "paragraph";
    public const TYPE_HEADING_1 = "heading_1";
    public const TYPE_HEADING_2 = "heading_2";
    public const TYPE_HEADING_3 = "heading_3";
    public const TYPE_CALLOUT = "callout";
    public const TYPE_QUOTE = "quote";
    public const TYPE_BULLETED_LIST_ITEM = "bulleted_list_item";
    public const TYPE_NUMBERED_LIST_ITEM = "numbered_list_item";
    public const TYPE_TO_DO = "to_do";
    public const TYPE_TOGGLE = "toggle";
    public const TYPE_CODE = "code";
    public const TYPE_CHILD_PAGE = "child_page";
    public const TYPE_CHILD_DATABASE = "child_database";
    public const TYPE_EMBED = "embed";
    public const TYPE_IMAGE = "image";
    public const TYPE_VIDEO = "video";
    public const TYPE_FILE = "file";
    public const TYPE_PDF = "pdf";
    public const TYPE_BOOKMARK = "bookmark";
    public const TYPE_EQUATION = "equation";
    public const TYPE_DIVIDER = "divider";
    public const TYPE_TABLE_OF_CONTENTS = "table_of_contents";
    public const TYPE_BREADCRUMB = "breadcrumb";

    private string $id;
    private DateTimeImmutable $createdTime;
    private DateTimeImmutable $lastEditedTime;
    private bool $archived;
    private bool $hasChildren;
    private string $type;

    private function __construct(
        string $id,
        DateTimeImmutable $createdTime,
        DateTimeImmutable $lastEditedTime,
        bool $archived,
        bool $hasChildren,
        string $type,
    ) {
        $this->id = $id;
        $this->createdTime = $createdTime;
        $this->lastEditedTime = $lastEditedTime;
        $this->archived = $archived;
        $this->hasChildren = $hasChildren;
        $this->type = $type;
    }

    public static function create(string $type): self
    {
        $now = new DateTimeImmutable("now");

        return new self("", $now, $now, false, false, $type);
    }

    /**
     * @param BlockJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        return new self(
            $array["id"],
            new DateTimeImmutable($array["created_time"]),
            new DateTimeImmutable($array["last_edited_time"]),
            $array["archived"],
            $array["has_children"],
            $array["type"],
        );
    }

    public function toArray(): array
    {
        return [
            // "id"               => $this->id !== "" ? $this->id : null,
            "object"           => "block",
            "created_time"     => $this->createdTime->format(Date::FORMAT),
            "last_edited_time" => $this->lastEditedTime->format(Date::FORMAT),
            "archived"         => $this->archived,
            "has_children"     => $this->hasChildren,
            "type"             => $this->type,
        ];
    }

    public function withHasChildren(bool $hasChildren): self
    {
        return new self(
            $this->id,
            $this->createdTime,
            $this->lastEditedTime,
            $this->archived,
            $hasChildren,
            $this->type,
        );
    }

    public function id(): string
    {
        return $this->id;
    }

    public function createdTime(): DateTimeImmutable
    {
        return $this->createdTime;
    }

    public function lastEditedType(): DateTimeImmutable
    {
        return $this->lastEditedTime;
    }

    public function archived(): bool
    {
        return $this->archived;
    }

    public function hasChildren(): bool
    {
        return $this->hasChildren;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function isParagraph(): bool
    {
        return $this->type === self::TYPE_PARAGRAPH;
    }

    public function isHeading1(): bool
    {
        return $this->type === self::TYPE_HEADING_1;
    }

    public function isHeading2(): bool
    {
        return $this->type === self::TYPE_HEADING_2;
    }

    public function isHeading3(): bool
    {
        return $this->type === self::TYPE_HEADING_3;
    }

    public function isCallout(): bool
    {
        return $this->type === self::TYPE_CALLOUT;
    }

    public function isQuote(): bool
    {
        return $this->type === self::TYPE_QUOTE;
    }

    public function isBulletedListItem(): bool
    {
        return $this->type === self::TYPE_BULLETED_LIST_ITEM;
    }

    public function isNumberedListItem(): bool
    {
        return $this->type === self::TYPE_NUMBERED_LIST_ITEM;
    }

    public function isToDo(): bool
    {
        return $this->type === self::TYPE_TO_DO;
    }

    public function isToggle(): bool
    {
        return $this->type === self::TYPE_TOGGLE;
    }

    public function isCode(): bool
    {
        return $this->type === self::TYPE_CODE;
    }

    public function isChildPage(): bool
    {
        return $this->type === self::TYPE_CHILD_PAGE;
    }

    public function isChildDatabase(): bool
    {
        return $this->type === self::TYPE_CHILD_DATABASE;
    }

    public function isEmbed(): bool
    {
        return $this->type === self::TYPE_EMBED;
    }

    public function isImage(): bool
    {
        return $this->type === self::TYPE_IMAGE;
    }

    public function isVideo(): bool
    {
        return $this->type === self::TYPE_VIDEO;
    }

    public function isFile(): bool
    {
        return $this->type === self::TYPE_FILE;
    }

    public function isPDF(): bool
    {
        return $this->type === self::TYPE_PDF;
    }

    public function isBookmark(): bool
    {
        return $this->type === self::TYPE_BOOKMARK;
    }

    public function isEquation(): bool
    {
        return $this->type === self::TYPE_EQUATION;
    }

    public function isDivider(): bool
    {
        return $this->type === self::TYPE_DIVIDER;
    }

    public function isTableOfContents(): bool
    {
        return $this->type === self::TYPE_TABLE_OF_CONTENTS;
    }

    public function isBreadcrumb(): bool
    {
        return $this->type === self::TYPE_BREADCRUMB;
    }
}
