<?php

namespace Notion\Databases;

use DateTimeImmutable;
use Notion\Common\Date;
use Notion\Common\Emoji;
use Notion\Common\File;
use Notion\Common\Icon;
use Notion\Common\RichText;
use Notion\Exceptions\DatabaseException;

/**
 * @psalm-import-type ChildDataSourceJson from ChildDataSource
 * @psalm-import-type EmojiJson from \Notion\Common\Emoji
 * @psalm-import-type FileJson from \Notion\Common\File
 * @psalm-import-type RichTextJson from \Notion\Common\RichText
 * @psalm-import-type DatabaseParentJson from DatabaseParent
 *
 * @psalm-type DatabaseJson = array{
 *      object: "database",
 *      id: string,
 *      data_sources: ChildDataSourceJson[],
 *      created_time: string,
 *      last_edited_time: string,
 *      title: RichTextJson[],
 *      description: RichTextJson[],
 *      icon: EmojiJson|FileJson|null,
 *      cover: FileJson|null,
 *      parent: DatabaseParentJson,
 *      url: string,
 *      is_inline: bool,
 * }
 *
 * @psalm-immutable
 */
class Database
{
    /**
     * @param ChildDataSource[] $dataSources
     * @param RichText[] $title
     * @param RichText[] $description
     */
    private function __construct(
        public readonly string $id,
        public readonly array $dataSources,
        public readonly DateTimeImmutable $createdTime,
        public readonly DateTimeImmutable $lastEditedTime,
        public readonly array $title,
        public readonly array $description,
        public readonly Icon|null $icon,
        public readonly File|null $cover,
        public readonly DatabaseParent $parent,
        public readonly string $url,
        public readonly bool $isInline,
    ) {
        if ($cover !== null && $cover->isInternal()) {
            throw DatabaseException::internalCover();
        }
    }

    public static function create(DatabaseParent $parent): self
    {
        $now = new DateTimeImmutable("now");

        return new self(
            "",
            [],
            $now,
            $now,
            [],
            [],
            null,
            null,
            $parent,
            "",
            false,
        );
    }

    /**
     * @param DatabaseJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        $dataSources = array_map(
            function (array $dataSourceArray): ChildDataSource {
                return ChildDataSource::fromArray($dataSourceArray);
            },
            $array["data_sources"] ?? [],
        );

        $title = array_map(
            function (array $richTextArray): RichText {
                return RichText::fromArray($richTextArray);
            },
            $array["title"],
        );
        $description = array_map(
            function (array $descriptionArray): RichText {
                return RichText::fromArray($descriptionArray);
            },
            $array["description"] ?? [],
        );

        $icon = null;
        if (is_array($array["icon"])) {
            $iconArray = $array["icon"];
            $iconType = $iconArray["type"];

            if ($iconType === "emoji") {
                /** @psalm-var EmojiJson $iconArray */
                $emoji = Emoji::fromArray($iconArray);
                $icon = Icon::fromEmoji($emoji);
            }

            if ($iconType === "file" || $iconType === "external") {
                /** @psalm-var FileJson $iconArray */
                $file = File::fromArray($iconArray);
                $icon = Icon::fromFile($file);
            }
        }

        $cover = isset($array["cover"]) ? File::fromArray($array["cover"]) : null;

        $parent = DatabaseParent::fromArray($array["parent"]);

        return new self(
            $array["id"],
            $dataSources,
            new DateTimeImmutable($array["created_time"]),
            new DateTimeImmutable($array["last_edited_time"]),
            $title,
            $description,
            $icon,
            $cover,
            $parent,
            $array["url"],
            $array["is_inline"],
        );
    }

    public function toArray(): array
    {
        return [
            "object"           => "database",
            "id"               => $this->id,
            "data_sources"     => array_map(fn(ChildDataSource $ds) => $ds->toArray(), $this->dataSources),
            "created_time"     => $this->createdTime->format(Date::FORMAT),
            "last_edited_time" => $this->lastEditedTime->format(Date::FORMAT),
            "title"            => array_map(fn(RichText $t) => $t->toArray(), $this->title),
            "description"      => array_map(fn(RichText $t) => $t->toArray(), $this->description),
            "icon"             => $this->icon?->toArray(),
            "cover"            => $this->cover?->toArray(),
            "parent"           => $this->parent->toArray(),
            "url"              => $this->url,
            "is_inline"        => $this->isInline,
        ];
    }

    /**
     * @psalm-assert-if-false null $this->icon
     */
    public function hasIcon(): bool
    {
        return $this->icon !== null;
    }

    public function changeTitle(string $title): self
    {
        return new self(
            $this->id,
            $this->dataSources,
            $this->createdTime,
            $this->lastEditedTime,
            [ RichText::fromString($title) ],
            $this->description,
            $this->icon,
            $this->cover,
            $this->parent,
            $this->url,
            $this->isInline,
        );
    }

    public function changeAdvancedTitle(RichText ...$title): self
    {
        return new self(
            $this->id,
            $this->dataSources,
            $this->createdTime,
            $this->lastEditedTime,
            $title,
            $this->description,
            $this->icon,
            $this->cover,
            $this->parent,
            $this->url,
            $this->isInline,
        );
    }

    public function changeIcon(Emoji|File|Icon $icon): self
    {
        if ($icon instanceof Emoji) {
            $icon = Icon::fromEmoji($icon);
        }

        if ($icon instanceof File) {
            $icon = Icon::fromFile($icon);
        }

        return new self(
            $this->id,
            $this->dataSources,
            $this->createdTime,
            $this->lastEditedTime,
            $this->title,
            $this->description,
            $icon,
            $this->cover,
            $this->parent,
            $this->url,
            $this->isInline,
        );
    }

    public function removeIcon(): self
    {
        return new self(
            $this->id,
            $this->dataSources,
            $this->createdTime,
            $this->lastEditedTime,
            $this->title,
            $this->description,
            null,
            $this->cover,
            $this->parent,
            $this->url,
            $this->isInline,
        );
    }

    public function changeCover(File $cover): self
    {
        return new self(
            $this->id,
            $this->dataSources,
            $this->createdTime,
            $this->lastEditedTime,
            $this->title,
            $this->description,
            $this->icon,
            $cover,
            $this->parent,
            $this->url,
            $this->isInline,
        );
    }

    public function removeCover(): self
    {
        return new self(
            $this->id,
            $this->dataSources,
            $this->createdTime,
            $this->lastEditedTime,
            $this->title,
            $this->description,
            $this->icon,
            null,
            $this->parent,
            $this->url,
            $this->isInline,
        );
    }

    public function changeParent(DatabaseParent $parent): self
    {
        return new self(
            $this->id,
            $this->dataSources,
            $this->createdTime,
            $this->lastEditedTime,
            $this->title,
            $this->description,
            $this->icon,
            $this->cover,
            $parent,
            $this->url,
            $this->isInline,
        );
    }

    public function enableInline(): self
    {
        return new self(
            $this->id,
            $this->dataSources,
            $this->createdTime,
            $this->lastEditedTime,
            $this->title,
            $this->description,
            $this->icon,
            $this->cover,
            $this->parent,
            $this->url,
            true,
        );
    }

    public function disableInline(): self
    {
        return new self(
            $this->id,
            $this->dataSources,
            $this->createdTime,
            $this->lastEditedTime,
            $this->title,
            $this->description,
            $this->icon,
            $this->cover,
            $this->parent,
            $this->url,
            false,
        );
    }
}
