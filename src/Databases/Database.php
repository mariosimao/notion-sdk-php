<?php

namespace Notion\Databases;

use DateTimeImmutable;
use Notion\Common\Date;
use Notion\Common\Emoji;
use Notion\Common\File;
use Notion\Common\Icon;
use Notion\Common\RichText;
use Notion\Databases\Properties\PropertyCollection;
use Notion\Databases\Properties\PropertyFactory;
use Notion\Databases\Properties\PropertyInterface;
use Notion\Databases\Properties\Status;
use Notion\Databases\Properties\Title;
use Notion\Exceptions\DatabaseException;

/**
 * @psalm-import-type EmojiJson from \Notion\Common\Emoji
 * @psalm-import-type FileJson from \Notion\Common\File
 * @psalm-import-type RichTextJson from \Notion\Common\RichText
 * @psalm-import-type PropertyMetadataJson from \Notion\Databases\Properties\PropertyMetadata
 * @psalm-import-type DatabaseParentJson from DatabaseParent
 *
 * @psalm-type DatabaseJson = array{
 *      object: "database",
 *      id: string,
 *      created_time: string,
 *      last_edited_time: string,
 *      title: RichTextJson[],
 *      description: RichTextJson[],
 *      icon: EmojiJson|FileJson|null,
 *      cover: FileJson|null,
 *      properties: array<string, PropertyMetadataJson>,
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
     * @param RichText[] $title
     * @param RichText[] $description
     * @param array<string, PropertyInterface> $properties
     */
    private function __construct(
        public readonly string $id,
        public readonly DateTimeImmutable $createdTime,
        public readonly DateTimeImmutable $lastEditedTime,
        public readonly array $title,
        public readonly array $description,
        public readonly Icon|null $icon,
        public readonly File|null $cover,
        public readonly array $properties,
        public readonly DatabaseParent $parent,
        public readonly string $url,
        public readonly bool $isInline,
    ) {
        if ($cover !== null && $cover->isInternal()) {
            throw DatabaseException::internalCover();
        }

        if (!$this->hasTitleProperty($properties)) {
            throw DatabaseException::noTitleProperty();
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
            [],
            null,
            null,
            [ "Title" => Title::create() ],
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

        $properties = [];
        foreach ($array["properties"] as $propertyName => $propertyArray) {
            $properties[$propertyName] = PropertyFactory::fromArray($propertyArray);
        }

        return new self(
            $array["id"],
            new DateTimeImmutable($array["created_time"]),
            new DateTimeImmutable($array["last_edited_time"]),
            $title,
            $description,
            $icon,
            $cover,
            $properties,
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
            "created_time"     => $this->createdTime->format(Date::FORMAT),
            "last_edited_time" => $this->lastEditedTime->format(Date::FORMAT),
            "title"            => array_map(fn(RichText $t) => $t->toArray(), $this->title),
            "description"      => array_map(fn(RichText $t) => $t->toArray(), $this->description),
            "icon"             => $this->icon?->toArray(),
            "cover"            => $this->cover?->toArray(),
            "properties"       => $this->propertiesToArray(),
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
            $this->createdTime,
            $this->lastEditedTime,
            [ RichText::fromString($title) ],
            $this->description,
            $this->icon,
            $this->cover,
            $this->properties,
            $this->parent,
            $this->url,
            $this->isInline,
        );
    }

    public function changeAdvancedTitle(RichText ...$title): self
    {
        return new self(
            $this->id,
            $this->createdTime,
            $this->lastEditedTime,
            $title,
            $this->description,
            $this->icon,
            $this->cover,
            $this->properties,
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
            $this->createdTime,
            $this->lastEditedTime,
            $this->title,
            $this->description,
            $icon,
            $this->cover,
            $this->properties,
            $this->parent,
            $this->url,
            $this->isInline,
        );
    }

    public function removeIcon(): self
    {
        return new self(
            $this->id,
            $this->createdTime,
            $this->lastEditedTime,
            $this->title,
            $this->description,
            null,
            $this->cover,
            $this->properties,
            $this->parent,
            $this->url,
            $this->isInline,
        );
    }

    public function changeCover(File $cover): self
    {
        return new self(
            $this->id,
            $this->createdTime,
            $this->lastEditedTime,
            $this->title,
            $this->description,
            $this->icon,
            $cover,
            $this->properties,
            $this->parent,
            $this->url,
            $this->isInline,
        );
    }

    public function removeCover(): self
    {
        return new self(
            $this->id,
            $this->createdTime,
            $this->lastEditedTime,
            $this->title,
            $this->description,
            $this->icon,
            null,
            $this->properties,
            $this->parent,
            $this->url,
            $this->isInline,
        );
    }

    public function properties(): PropertyCollection
    {
        return PropertyCollection::create(...$this->properties);
    }

    public function addProperty(PropertyInterface $property): self
    {
        return new self(
            $this->id,
            $this->createdTime,
            $this->lastEditedTime,
            $this->title,
            $this->description,
            $this->icon,
            $this->cover,
            $this->properties()->add($property)->getAll(),
            $this->parent,
            $this->url,
            $this->isInline,
        );
    }

    public function removePropertyByName(string $propertyName): self
    {
        return new self(
            $this->id,
            $this->createdTime,
            $this->lastEditedTime,
            $this->title,
            $this->description,
            $this->icon,
            $this->cover,
            $this->properties()->remove($propertyName)->getAll(),
            $this->parent,
            $this->url,
            $this->isInline,
        );
    }

    public function changeProperty(PropertyInterface $property): self
    {
        return new self(
            $this->id,
            $this->createdTime,
            $this->lastEditedTime,
            $this->title,
            $this->description,
            $this->icon,
            $this->cover,
            $this->properties()->change($property)->getAll(),
            $this->parent,
            $this->url,
            $this->isInline,
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
            $this->description,
            $this->icon,
            $this->cover,
            PropertyCollection::create(...$properties)->getAll(),
            $this->parent,
            $this->url,
            $this->isInline,
        );
    }

    public function changeParent(DatabaseParent $parent): self
    {
        return new self(
            $this->id,
            $this->createdTime,
            $this->lastEditedTime,
            $this->title,
            $this->description,
            $this->icon,
            $this->cover,
            $this->properties,
            $parent,
            $this->url,
            $this->isInline,
        );
    }

    public function enableInline(): self
    {
        return new self(
            $this->id,
            $this->createdTime,
            $this->lastEditedTime,
            $this->title,
            $this->description,
            $this->icon,
            $this->cover,
            $this->properties,
            $this->parent,
            $this->url,
            true,
        );
    }

    public function disableInline(): self
    {
        return new self(
            $this->id,
            $this->createdTime,
            $this->lastEditedTime,
            $this->title,
            $this->description,
            $this->icon,
            $this->cover,
            $this->properties,
            $this->parent,
            $this->url,
            false,
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

    private function propertiesToArray(): array
    {
        $array = [];

        $properties = $this->properties;
        foreach ($properties as $name => $property) {
            if ($property instanceof Status) {
                continue;
            }
            $array[$name] = $property->toArray();
        }

        return $array;
    }
}
