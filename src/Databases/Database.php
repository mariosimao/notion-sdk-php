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
 *      in_trash?: bool,
 *      title: RichTextJson[],
 *      description: RichTextJson[],
 *      icon: EmojiJson|FileJson|null,
 *      cover: FileJson|null,
 *      parent: DatabaseParentJson,
 *      url: string,
 *      is_inline: bool,
 *      database_type?: string|null,
 * }
 *
 * @psalm-immutable
 */
final readonly class Database
{
    /**
     * @param ChildDataSource[] $dataSources
     * @param RichText[] $title
     * @param RichText[] $description
     */
    private function __construct(
        public string $id,
        public array $dataSources,
        public DateTimeImmutable $createdTime,
        public DateTimeImmutable $lastEditedTime,
        public bool $inTrash,
        public array $title,
        public array $description,
        public Icon|null $icon,
        public File|null $cover,
        public DatabaseParent $parent,
        public string $url,
        public bool $isInline,
        public DatabaseType|null $databaseType,
    ) {
        if ($cover !== null && $cover->isInternal()) {
            throw DatabaseException::internalCover();
        }
    }

    /**
     * Creates a database to be saved with `Client::create()`.
     *
     * Pass a `$databaseType` to create a typed database using Notion's
     * canonical schema. Only `DatabaseType::Tasks`, `DatabaseType::Projects`
     * and `DatabaseType::Skills` are supported. Typed databases cannot be
     * created with initial properties and, if no title is set, are named after
     * their type.
     */
    public static function create(DatabaseParent $parent, DatabaseType|null $databaseType = null): self
    {
        if ($databaseType !== null && !$databaseType->isCreatable()) {
            throw DatabaseException::databaseTypeNotCreatable($databaseType);
        }

        $now = new DateTimeImmutable("now");

        return new self(
            "",
            [],
            $now,
            $now,
            false,
            [],
            [],
            null,
            null,
            $parent,
            "",
            false,
            $databaseType,
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

        $databaseType = isset($array["database_type"])
            ? DatabaseType::tryFrom($array["database_type"]) ?? DatabaseType::Unknown
            : null;

        return new self(
            $array["id"],
            $dataSources,
            new DateTimeImmutable($array["created_time"]),
            new DateTimeImmutable($array["last_edited_time"]),
            $array["in_trash"] ?? false,
            $title,
            $description,
            $icon,
            $cover,
            $parent,
            $array["url"],
            $array["is_inline"],
            $databaseType,
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
            "in_trash"         => $this->inTrash,
            "title"            => array_map(fn(RichText $t) => $t->toArray(), $this->title),
            "description"      => array_map(fn(RichText $t) => $t->toArray(), $this->description),
            "icon"             => $this->icon?->toArray(),
            "cover"            => $this->cover?->toArray(),
            "parent"           => $this->parent->toArray(),
            "url"              => $this->url,
            "is_inline"        => $this->isInline,
            "database_type"    => $this->databaseType?->value,
        ];
    }

    /**
     * @psalm-assert-if-false null $this->icon
     */
    public function hasIcon(): bool
    {
        return $this->icon !== null;
    }

    /**
     * @psalm-assert-if-true !null $this->databaseType
     */
    public function isTyped(): bool
    {
        return $this->databaseType !== null;
    }

    public function archive(): self
    {
        return new self(
            $this->id,
            $this->dataSources,
            $this->createdTime,
            $this->lastEditedTime,
            true,
            $this->title,
            $this->description,
            $this->icon,
            $this->cover,
            $this->parent,
            $this->url,
            $this->isInline,
            $this->databaseType,
        );
    }

    public function restore(): self
    {
        return new self(
            $this->id,
            $this->dataSources,
            $this->createdTime,
            $this->lastEditedTime,
            false,
            $this->title,
            $this->description,
            $this->icon,
            $this->cover,
            $this->parent,
            $this->url,
            $this->isInline,
            $this->databaseType,
        );
    }

    public function changeTitle(string $title): self
    {
        return new self(
            $this->id,
            $this->dataSources,
            $this->createdTime,
            $this->lastEditedTime,
            $this->inTrash,
            [ RichText::fromString($title) ],
            $this->description,
            $this->icon,
            $this->cover,
            $this->parent,
            $this->url,
            $this->isInline,
            $this->databaseType,
        );
    }

    public function changeAdvancedTitle(RichText ...$title): self
    {
        return new self(
            $this->id,
            $this->dataSources,
            $this->createdTime,
            $this->lastEditedTime,
            $this->inTrash,
            $title,
            $this->description,
            $this->icon,
            $this->cover,
            $this->parent,
            $this->url,
            $this->isInline,
            $this->databaseType,
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
            $this->inTrash,
            $this->title,
            $this->description,
            $icon,
            $this->cover,
            $this->parent,
            $this->url,
            $this->isInline,
            $this->databaseType,
        );
    }

    public function removeIcon(): self
    {
        return new self(
            $this->id,
            $this->dataSources,
            $this->createdTime,
            $this->lastEditedTime,
            $this->inTrash,
            $this->title,
            $this->description,
            null,
            $this->cover,
            $this->parent,
            $this->url,
            $this->isInline,
            $this->databaseType,
        );
    }

    public function changeCover(File $cover): self
    {
        return new self(
            $this->id,
            $this->dataSources,
            $this->createdTime,
            $this->lastEditedTime,
            $this->inTrash,
            $this->title,
            $this->description,
            $this->icon,
            $cover,
            $this->parent,
            $this->url,
            $this->isInline,
            $this->databaseType,
        );
    }

    public function removeCover(): self
    {
        return new self(
            $this->id,
            $this->dataSources,
            $this->createdTime,
            $this->lastEditedTime,
            $this->inTrash,
            $this->title,
            $this->description,
            $this->icon,
            null,
            $this->parent,
            $this->url,
            $this->isInline,
            $this->databaseType,
        );
    }

    public function changeParent(DatabaseParent $parent): self
    {
        return new self(
            $this->id,
            $this->dataSources,
            $this->createdTime,
            $this->lastEditedTime,
            $this->inTrash,
            $this->title,
            $this->description,
            $this->icon,
            $this->cover,
            $parent,
            $this->url,
            $this->isInline,
            $this->databaseType,
        );
    }

    public function enableInline(): self
    {
        return new self(
            $this->id,
            $this->dataSources,
            $this->createdTime,
            $this->lastEditedTime,
            $this->inTrash,
            $this->title,
            $this->description,
            $this->icon,
            $this->cover,
            $this->parent,
            $this->url,
            true,
            $this->databaseType,
        );
    }

    public function disableInline(): self
    {
        return new self(
            $this->id,
            $this->dataSources,
            $this->createdTime,
            $this->lastEditedTime,
            $this->inTrash,
            $this->title,
            $this->description,
            $this->icon,
            $this->cover,
            $this->parent,
            $this->url,
            false,
            $this->databaseType,
        );
    }
}
