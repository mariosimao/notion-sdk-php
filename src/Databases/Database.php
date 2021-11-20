<?php

namespace Notion\Databases;

use DateTimeImmutable;
use Notion\Common\Date;
use Notion\Common\Emoji;
use Notion\Common\File;
use Notion\Common\RichText;
use Notion\Databases\Properties\Factory;
use Notion\Databases\Properties\PropertyInterface;
use Notion\Databases\Properties\Title;
use Notion\NotionException;

/**
 * @psalm-import-type EmojiJson from \Notion\Common\Emoji
 * @psalm-import-type FileJson from \Notion\Common\File
 * @psalm-import-type RichTextJson from \Notion\Common\RichText
 * @psalm-import-type PropertyJson from \Notion\Databases\Properties\Property
 * @psalm-import-type DatabaseParentJson from DatabaseParent
 *
 * @psalm-type DatabaseJson = array{
 *      id: string,
 *      created_time: string,
 *      last_edited_time: string,
 *      title: list<RichTextJson>,
 *      icon: EmojiJson|FileJson|null,
 *      cover: FileJson|null,
 *      properties: array<string, PropertyJson>,
 *      parent: DatabaseParentJson,
 *      url: string,
 * }
 *
 * @psalm-immutable
 */
class Database
{
    private string $id;
    private DateTimeImmutable $createdTime;
    private DateTimeImmutable $lastEditedTime;
    /** @var list<RichText> */
    private array $title;
    private Emoji|File|null $icon;
    private File|null $cover;
    /** @var array<string, PropertyInterface> */
    private array $properties;
    private DatabaseParent $parent;
    private string $url;

    /**
     * @param list<RichText> $title
     * @param array<string, PropertyInterface> $properties
     */
    private function __construct(
        string $id,
        DateTimeImmutable $createdTime,
        DateTimeImmutable $lastEditedTime,
        array $title,
        Emoji|File|null $icon,
        File|null $cover,
        array $properties,
        DatabaseParent $parent,
        string $url,
    ) {
        if ($cover !== null && $cover->isInternal()) {
            throw new \Exception("Internal cover image is not supported");
        }

        if (!$this->hasTitleProperty($properties)) {
            throw new NotionException("A database must have a title property", "validation_error");
        }

        $this->id = $id;
        $this->createdTime = $createdTime;
        $this->lastEditedTime = $lastEditedTime;
        $this->title = $title;
        $this->icon = $icon;
        $this->cover = $cover;
        $this->properties = $properties;
        $this->parent = $parent;
        $this->url = $url;
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
                $icon = Emoji::fromArray($iconArray);
            }

            if ($iconType === "file" || $iconType === "external") {
                /** @psalm-var FileJson $iconArray */
                $icon = File::fromArray($iconArray);
            }
        }

        $cover = isset($array["cover"]) ? File::fromArray($array["cover"]) : null;

        $parent = DatabaseParent::fromArray($array["parent"]);

        $properties = [];
        foreach ($array["properties"] as $propertyName => $propertyArray) {
            $properties[$propertyName] = Factory::fromArray($propertyArray);
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

    public function id(): string
    {
        return $this->id;
    }

    public function createdTime(): DateTimeImmutable
    {
        return $this->createdTime;
    }

    public function lastEditedTime(): DateTimeImmutable
    {
        return $this->lastEditedTime;
    }

    /** @return list<RichText> */
    public function title(): array
    {
        return $this->title;
    }

    public function icon(): Emoji|File|null
    {
        return $this->icon;
    }

    /**
     * @psalm-assert-if-true Emoji $this->icon
     * @psalm-assert-if-true Emoji $this->icon()
     */
    public function iconIsEmoji(): bool
    {
        return $this->icon::class === Emoji::class;
    }

    /**
     * @psalm-assert-if-true File $this->icon
     * @psalm-assert-if-true File $this->icon()
     */
    public function iconIsFile(): bool
    {
        return $this->icon::class === File::class;
    }

    /**
     * @psalm-assert-if-false null $this->icon
     * @psalm-assert-if-false null $this->icon()
     */
    public function hasIcon(): bool
    {
        return $this->icon !== null;
    }

    public function cover(): File|null
    {
        return $this->cover;
    }

    /** @return array<string, PropertyInterface> */
    public function properties(): array
    {
        return $this->properties;
    }

    public function parent(): DatabaseParent
    {
        return $this->parent;
    }

    public function url(): string
    {
        return $this->url;
    }

    /** @param list<RichText> $title */
    public function withAdvancedTitle(array $title): self
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

    public function withIcon(Emoji|File $icon): self
    {
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

    public function withoutIcon(): self
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

    public function withCover(File $cover): self
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

    public function withoutCover(): self
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
        $name = $property->property()->name();
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
    public function withProperties(array $properties): self
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

    public function withParent(DatabaseParent $parent): self
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
