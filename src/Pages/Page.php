<?php

namespace Notion\Pages;

use DateTimeImmutable;
use Notion\Common\Date;
use Notion\Common\Emoji;
use Notion\Common\File;
use Notion\Common\Icon;
use Notion\Pages\Properties\PropertyCollection;
use Notion\Pages\Properties\PropertyFactory;
use Notion\Pages\Properties\PropertyInterface;
use Notion\Pages\Properties\Title;

/**
 * @psalm-import-type EmojiJson from \Notion\Common\Emoji
 * @psalm-import-type FileJson from \Notion\Common\File
 * @psalm-import-type PropertyMetadataJson from \Notion\Pages\Properties\PropertyMetadata
 * @psalm-import-type PageParentJson from PageParent
 *
 * @psalm-type PageJson = array{
 *      object: "page",
 *      id: string,
 *      created_time: string,
 *      last_edited_time: string,
 *      archived: bool,
 *      icon: EmojiJson|FileJson|null,
 *      cover: FileJson|null,
 *      properties: array<string, PropertyMetadataJson>,
 *      parent: PageParentJson,
 *      url: string,
 * }
 *
 * @psalm-immutable
 */
class Page
{
    /**
     * @param array<string, PropertyInterface> $properties
     */
    private function __construct(
        public readonly string $id,
        public readonly DateTimeImmutable $createdTime,
        public readonly DateTimeImmutable $lastEditedTime,
        public readonly bool $archived,
        public readonly Icon|null $icon,
        public readonly File|null $cover,
        public readonly array $properties,
        public readonly PageParent $parent,
        public readonly string $url
    ) {
    }

    public static function create(PageParent $parent): self
    {
        $now = new DateTimeImmutable("now");

        return new self("", $now, $now, false, null, null, [], $parent, "");
    }


    /**
     * @param PageJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
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

        $parent = PageParent::fromArray($array["parent"]);

        $properties = [];
        foreach ($array["properties"] as $propertyName => $propertyArray) {
            $properties[$propertyName] = PropertyFactory::fromArray($propertyArray);
        }

        return new self(
            $array["id"],
            new DateTimeImmutable($array["created_time"]),
            new DateTimeImmutable($array["last_edited_time"]),
            $array["archived"],
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
            "object"           => "page",
            "id"               => $this->id,
            "created_time"     => $this->createdTime->format(Date::FORMAT),
            "last_edited_time" => $this->lastEditedTime->format(Date::FORMAT),
            "archived"         => $this->archived,
            "icon"             => $this->icon?->toArray(),
            "cover"            => $this->cover?->toArray(),
            "properties"       => array_map(fn($p) => $p->toArray(), $this->properties),
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

    public function archive(): self
    {
        return new self(
            $this->id,
            $this->createdTime,
            $this->lastEditedTime,
            true,
            $this->icon,
            $this->cover,
            $this->properties,
            $this->parent,
            $this->url,
        );
    }

    public function unarchive(): self
    {
        return new self(
            $this->id,
            $this->createdTime,
            $this->lastEditedTime,
            false,
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
            $this->archived,
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
            $this->archived,
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
            $this->archived,
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
            $this->archived,
            $this->icon,
            null,
            $this->properties,
            $this->parent,
            $this->url,
        );
    }

    public function properties(): PropertyCollection
    {
        return PropertyCollection::create($this->properties);
    }

    public function getProperty(string $propertyName): PropertyInterface
    {
        return $this->properties()->get($propertyName);
    }

    /** @deprecated 1.4.0 Typo. Use `getProperty()` instead. */
    public function getProprety(string $propertyName): PropertyInterface
    {
        return $this->getProperty($propertyName);
    }

    public function addProperty(string $name, PropertyInterface $property): self
    {
        return new self(
            $this->id,
            $this->createdTime,
            $this->lastEditedTime,
            $this->archived,
            $this->icon,
            $this->cover,
            $this->properties()->add($name, $property)->getAll(),
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
            $this->archived,
            $this->icon,
            $this->cover,
            PropertyCollection::create($properties)->getAll(),
            $this->parent,
            $this->url,
        );
    }

    public function changeTitle(string $title): self
    {
        $property = Title::fromString($title);
        $key = $this->properties()->titleKey();

        return $this->addProperty($key, $property);
    }

    public function title(): Title|null
    {
        return $this->properties()->title();
    }

    public function changeParent(PageParent $parent): self
    {
        return new self(
            $this->id,
            $this->createdTime,
            $this->lastEditedTime,
            $this->archived,
            $this->icon,
            $this->cover,
            $this->properties,
            $parent,
            $this->url,
        );
    }
}
