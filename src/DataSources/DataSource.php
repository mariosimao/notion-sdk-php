<?php

namespace Notion\DataSources;

use DateTimeImmutable;
use Notion\Common\Date;
use Notion\Common\Emoji;
use Notion\Common\File;
use Notion\Common\Icon;
use Notion\Common\RichText;
use Notion\DataSources\Properties\PropertyCollection;
use Notion\DataSources\Properties\PropertyFactory;
use Notion\DataSources\Properties\PropertyInterface;
use Notion\DataSources\Properties\Status;
use Notion\DataSources\Properties\Title;

/**
 * @psalm-import-type EmojiJson from \Notion\Common\Emoji
 * @psalm-import-type FileJson from \Notion\Common\File
 * @psalm-import-type RichTextJson from \Notion\Common\RichText
 * @psalm-import-type PropertyMetadataJson from \Notion\DataSources\Properties\PropertyMetadata
 * @psalm-import-type DataSourceParentJson from DataSourceParent
 *
 * @psalm-type DataSourceJson = array{
 *      object: "data_source",
 *      id: string,
 *      created_time: string,
 *      last_edited_time: string,
 *      title: RichTextJson[],
 *      description: RichTextJson[],
 *      icon: EmojiJson|FileJson|null,
 *      properties: array<string, PropertyMetadataJson>,
 *      parent: DataSourceParentJson,
 *      url: string,
 * }
 *
 * @psalm-immutable
 */
class DataSource
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
        public readonly array $properties,
        public readonly DataSourceParent $parent,
        public readonly string $url,
    ) {
    }

    public static function create(DataSourceParent $parent): self
    {
        $now = new DateTimeImmutable("now");

        return new self(
            "",
            $now,
            $now,
            [],
            [],
            null,
            [ "Title" => Title::create() ],
            $parent,
            "",
        );
    }

    /**
     * @param DataSourceJson $array
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

        $parent = DataSourceParent::fromArray($array["parent"]);

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
            $properties,
            $parent,
            $array["url"],
        );
    }

    public function toArray(): array
    {
        return [
            "object"           => "data_source",
            "id"               => $this->id,
            "created_time"     => $this->createdTime->format(Date::FORMAT),
            "last_edited_time" => $this->lastEditedTime->format(Date::FORMAT),
            "title"            => array_map(fn(RichText $t) => $t->toArray(), $this->title),
            "description"      => array_map(fn(RichText $t) => $t->toArray(), $this->description),
            "icon"             => $this->icon?->toArray(),
            "properties"       => $this->propertiesToArray(),
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

    public function changeTitle(string $title): self
    {
        return new self(
            $this->id,
            $this->createdTime,
            $this->lastEditedTime,
            [ RichText::fromString($title) ],
            $this->description,
            $this->icon,
            $this->properties,
            $this->parent,
            $this->url,
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
            $this->description,
            $icon,
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
            $this->description,
            null,
            $this->properties,
            $this->parent,
            $this->url,
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
            $this->properties()->add($property)->getAll(),
            $this->parent,
            $this->url,
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
            $this->properties()->remove($propertyName)->getAll(),
            $this->parent,
            $this->url,
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
            $this->properties()->change($property)->getAll(),
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
            $this->description,
            $this->icon,
            PropertyCollection::create(...$properties)->getAll(),
            $this->parent,
            $this->url,
        );
    }

    public function changeParent(DataSourceParent $parent): self
    {
        return new self(
            $this->id,
            $this->createdTime,
            $this->lastEditedTime,
            $this->title,
            $this->description,
            $this->icon,
            $this->properties,
            $parent,
            $this->url,
        );
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
