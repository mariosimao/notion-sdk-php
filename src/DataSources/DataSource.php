<?php

namespace Notion\DataSources;

use DateTimeImmutable;
use Notion\Common\Date;
use Notion\Common\Emoji;
use Notion\Common\File;
use Notion\Common\Icon;
use Notion\Common\NativeIcon;
use Notion\Common\RichText;
use Notion\Databases\DatabaseParent;
use Notion\DataSources\Properties\PropertyCollection;
use Notion\DataSources\Properties\PropertyFactory;
use Notion\DataSources\Properties\PropertyInterface;
use Notion\DataSources\Properties\Status;
use Notion\DataSources\Properties\Title;
use Notion\Users\User;

/**
 * @psalm-import-type IconJson from \Notion\Common\Icon
 * @psalm-import-type FileJson from \Notion\Common\File
 * @psalm-import-type RichTextJson from \Notion\Common\RichText
 * @psalm-import-type PropertyMetadataJson from \Notion\DataSources\Properties\PropertyMetadata
 * @psalm-import-type DataSourceParentJson from DataSourceParent
 * @psalm-import-type DatabaseParentJson from \Notion\Databases\DatabaseParent
 * @psalm-import-type UserJson from \Notion\Users\User
 *
 * @psalm-type DataSourceJson = array{
 *      object: "data_source",
 *      id: string,
 *      created_time: string,
 *      last_edited_time: string,
 *      created_by?: UserJson,
 *      last_edited_by?: UserJson,
 *      in_trash?: bool,
 *      title: RichTextJson[],
 *      description: RichTextJson[],
 *      icon: IconJson|null,
 *      properties: array<string, PropertyMetadataJson>,
 *      parent: DataSourceParentJson,
 *      database_parent?: DatabaseParentJson|null,
 *      url: string,
 * }
 *
 * @psalm-immutable
 */
final readonly class DataSource
{
    /**
     * @param RichText[] $title
     * @param RichText[] $description
     * @param array<string, PropertyInterface> $properties
     */
    private function __construct(
        public string $id,
        public DateTimeImmutable $createdTime,
        public DateTimeImmutable $lastEditedTime,
        public User|null $createdBy,
        public User|null $lastEditedBy,
        public bool $inTrash,
        public array $title,
        public array $description,
        public Icon|null $icon,
        public array $properties,
        public DataSourceParent $parent,
        public string $url,
        public DatabaseParent|null $databaseParent = null,
    ) {
    }

    public static function create(DataSourceParent $parent): self
    {
        $now = new DateTimeImmutable("now");

        return new self(
            "",
            $now,
            $now,
            null,
            null,
            false,
            [],
            [],
            null,
            [ "Title" => Title::create() ],
            $parent,
            "",
            null,
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

        $icon = is_array($array["icon"]) ? Icon::tryFromArray($array["icon"]) : null;

        $parent = DataSourceParent::fromArray($array["parent"]);

        $databaseParent = isset($array["database_parent"])
            ? DatabaseParent::fromArray($array["database_parent"])
            : null;

        $properties = [];
        foreach ($array["properties"] as $propertyName => $propertyArray) {
            $properties[$propertyName] = PropertyFactory::fromArray($propertyArray);
        }

        return new self(
            $array["id"],
            new DateTimeImmutable($array["created_time"]),
            new DateTimeImmutable($array["last_edited_time"]),
            isset($array["created_by"]) ? User::fromArray($array["created_by"]) : null,
            isset($array["last_edited_by"]) ? User::fromArray($array["last_edited_by"]) : null,
            $array["in_trash"] ?? false,
            $title,
            $description,
            $icon,
            $properties,
            $parent,
            $array["url"],
            $databaseParent,
        );
    }

    public function toArray(): array
    {
        $array = [
            "object"           => "data_source",
            "id"               => $this->id,
            "created_time"     => $this->createdTime->format(Date::FORMAT),
            "last_edited_time" => $this->lastEditedTime->format(Date::FORMAT),
            "in_trash"         => $this->inTrash,
            "title"            => array_map(fn(RichText $t) => $t->toArray(), $this->title),
            "description"      => array_map(fn(RichText $t) => $t->toArray(), $this->description),
            "icon"             => $this->icon?->toArray(),
            "properties"       => $this->propertiesToArray(),
            "parent"           => $this->parent->toArray(),
            "database_parent"  => $this->databaseParent?->toArray(),
            "url"              => $this->url,
        ];

        if ($this->createdBy !== null) {
            $array["created_by"] = $this->createdBy->toArray();
        }

        if ($this->lastEditedBy !== null) {
            $array["last_edited_by"] = $this->lastEditedBy->toArray();
        }

        return $array;
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
            $this->createdBy,
            $this->lastEditedBy,
            true,
            $this->title,
            $this->description,
            $this->icon,
            $this->properties,
            $this->parent,
            $this->url,
            $this->databaseParent,
        );
    }

    public function restore(): self
    {
        return new self(
            $this->id,
            $this->createdTime,
            $this->lastEditedTime,
            $this->createdBy,
            $this->lastEditedBy,
            false,
            $this->title,
            $this->description,
            $this->icon,
            $this->properties,
            $this->parent,
            $this->url,
            $this->databaseParent,
        );
    }

    public function delete(): self
    {
        return $this->archive();
    }

    public function changeTitle(string $title): self
    {
        return new self(
            $this->id,
            $this->createdTime,
            $this->lastEditedTime,
            $this->createdBy,
            $this->lastEditedBy,
            $this->inTrash,
            [ RichText::fromString($title) ],
            $this->description,
            $this->icon,
            $this->properties,
            $this->parent,
            $this->url,
            $this->databaseParent,
        );
    }

    public function changeAdvancedTitle(RichText ...$title): self
    {
        return new self(
            $this->id,
            $this->createdTime,
            $this->lastEditedTime,
            $this->createdBy,
            $this->lastEditedBy,
            $this->inTrash,
            $title,
            $this->description,
            $this->icon,
            $this->properties,
            $this->parent,
            $this->url,
            $this->databaseParent,
        );
    }

    public function changeIcon(Emoji|File|NativeIcon|Icon $icon): self
    {
        $icon = Icon::from($icon);

        return new self(
            $this->id,
            $this->createdTime,
            $this->lastEditedTime,
            $this->createdBy,
            $this->lastEditedBy,
            $this->inTrash,
            $this->title,
            $this->description,
            $icon,
            $this->properties,
            $this->parent,
            $this->url,
            $this->databaseParent,
        );
    }

    public function removeIcon(): self
    {
        return new self(
            $this->id,
            $this->createdTime,
            $this->lastEditedTime,
            $this->createdBy,
            $this->lastEditedBy,
            $this->inTrash,
            $this->title,
            $this->description,
            null,
            $this->properties,
            $this->parent,
            $this->url,
            $this->databaseParent,
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
            $this->createdBy,
            $this->lastEditedBy,
            $this->inTrash,
            $this->title,
            $this->description,
            $this->icon,
            $this->properties()->add($property)->getAll(),
            $this->parent,
            $this->url,
            $this->databaseParent,
        );
    }

    public function removePropertyByName(string $propertyName): self
    {
        return new self(
            $this->id,
            $this->createdTime,
            $this->lastEditedTime,
            $this->createdBy,
            $this->lastEditedBy,
            $this->inTrash,
            $this->title,
            $this->description,
            $this->icon,
            $this->properties()->remove($propertyName)->getAll(),
            $this->parent,
            $this->url,
            $this->databaseParent,
        );
    }

    public function changeProperty(PropertyInterface $property): self
    {
        return new self(
            $this->id,
            $this->createdTime,
            $this->lastEditedTime,
            $this->createdBy,
            $this->lastEditedBy,
            $this->inTrash,
            $this->title,
            $this->description,
            $this->icon,
            $this->properties()->change($property)->getAll(),
            $this->parent,
            $this->url,
            $this->databaseParent,
        );
    }

    /** @param array<string, PropertyInterface> $properties */
    public function changeProperties(array $properties): self
    {
        return new self(
            $this->id,
            $this->createdTime,
            $this->lastEditedTime,
            $this->createdBy,
            $this->lastEditedBy,
            $this->inTrash,
            $this->title,
            $this->description,
            $this->icon,
            PropertyCollection::create(...$properties)->getAll(),
            $this->parent,
            $this->url,
            $this->databaseParent,
        );
    }

    public function changeParent(DataSourceParent $parent): self
    {
        return new self(
            $this->id,
            $this->createdTime,
            $this->lastEditedTime,
            $this->createdBy,
            $this->lastEditedBy,
            $this->inTrash,
            $this->title,
            $this->description,
            $this->icon,
            $this->properties,
            $parent,
            $this->url,
            $this->databaseParent,
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
