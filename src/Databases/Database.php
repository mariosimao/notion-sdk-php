<?php

namespace Notion\Databases;

use DateTimeImmutable;
use Notion\Common\Date;
use Notion\Common\Emoji;
use Notion\Common\File;
use Notion\Common\Icon;
use Notion\Common\RichText;
use Notion\Databases\Properties\PropertyFactory;
use Notion\Databases\Properties\PropertyInterface;
use Notion\Databases\Properties\Title;
use Notion\NotionException;

/**
 * @psalm-import-type EmojiJson from \Notion\Common\Emoji
 * @psalm-import-type FileJson from \Notion\Common\File
 * @psalm-import-type RichTextJson from \Notion\Common\RichText
 * @psalm-import-type PropertyMetadataJson from \Notion\Databases\Properties\PropertyMetadata
 * @psalm-import-type DatabaseParentJson from DatabaseParent
 *
 * @psalm-type DatabaseJson = array{
 *      id: string,
 *      created_time: string,
 *      last_edited_time: string,
 *      title: list<RichTextJson>,
 *      icon: EmojiJson|FileJson|null,
 *      cover: FileJson|null,
 *      properties: array<string, PropertyMetadataJson>,
 *      parent: DatabaseParentJson,
 *      url: string,
 * }
 *
 * @psalm-immutable
 */
class Database
{
    /**
     * @param RichText[] $title
     * @param array<string, PropertyInterface> $properties
     */
    private function __construct(
        public readonly string $id,
        public readonly DateTimeImmutable $createdTime,
        public readonly DateTimeImmutable $lastEditedTime,
        public readonly array $title,
        public readonly Icon|null $icon,
        public readonly File|null $cover,
        public readonly array $properties,
        public readonly DatabaseParent $parent,
        public readonly string $url,
    ) {
        if ($cover !== null && $cover->isInternal()) {
            throw new \Exception("Internal cover image is not supported");
        }

        if (!$this->hasTitleProperty($properties)) {
            throw new NotionException("A database must have a title property", "validation_error");
        }
    }

    public static function create(DatabaseParent $parent): self
    {
        $now = new DateTimeImmutable("now");

        return new self(
            "",
            $now,
            $now,
            [],
            null,
            null,
            [ "Title" => Title::create() ],
            $parent,
            ""
        );
    }

    /**
     * @param DatabaseJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        $title = array_map(
            function (array $richTextArray): RichText {
                return RichText::fromArray($richTextArray);
            },
            $array["title"],
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

        $properties = [];
        foreach ($array["properties"] as $propertyName => $propertyArray) {
            $properties[$propertyName] = PropertyFactory::fromArray($propertyArray);
        }

        return new self(
            $array["id"],
            new DateTimeImmutable($array["created_time"]),
            new DateTimeImmutable($array["last_edited_time"]),
            $title,
            $icon,
            $cover,
            $properties,
            $parent,
            $array["url"],
        );
    }

    public function toArray(): array
    {
        return [
            "object"           => "database",
            "id"               => $this->id,
            "created_time"     => $this->createdTime->format(Date::FORMAT),
            "last_edited_time" => $this->lastEditedTime->format(Date::FORMAT),
            "title"            => array_map(fn(RichText $t) => $t->toArray(), $this->title),
            "icon"             => $this->icon?->toArray(),
            "cover"            => $this->cover?->toArray(),
            "properties"       => array_map(fn(PropertyInterface $p) => $p->toArray(), $this->properties),
            "parent"           => $this->parent->toArray(),
            "url"              => $this->url,
        ];
    }

    /**
     * @psalm-assert-if-false null $this->icon
     */
    public function hasIcon(): bool
    {
        return $this->icon !== null;
    }

    public function changeAdvancedTitle(RichText ...$title): self
    {
        return new self(
            $this->id,
            $this->createdTime,
            $this->lastEditedTime,
            $title,
            $this->icon,
            $this->cover,
            $this->properties,
            $this->parent,
            $this->url,
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
            $this->createdTime,
            $this->lastEditedTime,
            $this->title,
            $icon,
            $this->cover,
            $this->properties,
            $this->parent,
            $this->url,
        );
    }

    public function removeIcon(): self
    {
        return new self(
            $this->id,
            $this->createdTime,
            $this->lastEditedTime,
            $this->title,
            null,
            $this->cover,
            $this->properties,
            $this->parent,
            $this->url,
        );
    }

    public function changeCover(File $cover): self
    {
        return new self(
            $this->id,
            $this->createdTime,
            $this->lastEditedTime,
            $this->title,
            $this->icon,
            $cover,
            $this->properties,
            $this->parent,
            $this->url,
        );
    }

    public function removeCover(): self
    {
        return new self(
            $this->id,
            $this->createdTime,
            $this->lastEditedTime,
            $this->title,
            $this->icon,
            null,
            $this->properties,
            $this->parent,
            $this->url,
        );
    }

    public function addProperty(PropertyInterface $property): self
    {
        $properties = $this->properties;
        $name = $property->metadata()->name;
        $properties[$name] = $property;

        return new self(
            $this->id,
            $this->createdTime,
            $this->lastEditedTime,
            $this->title,
            $this->icon,
            $this->cover,
            $properties,
            $this->parent,
            $this->url,
        );
    }

    /** @param array<string, PropertyInterface> $properties */
    public function changeProperties(array $properties): self
    {
        return new self(
            $this->id,
            $this->createdTime,
            $this->lastEditedTime,
            $this->title,
            $this->icon,
            $this->cover,
            $properties,
            $this->parent,
            $this->url,
        );
    }

    public function changeParent(DatabaseParent $parent): self
    {
        return new self(
            $this->id,
            $this->createdTime,
            $this->lastEditedTime,
            $this->title,
            $this->icon,
            $this->cover,
            $this->properties,
            $parent,
            $this->url,
        );
    }

    /** @param array<string, PropertyInterface> $properties */
    private function hasTitleProperty(array $properties): bool
    {
        foreach ($properties as $property) {
            if ($property instanceof Title) {
                return true;
            }
        }

        return false;
    }
}
