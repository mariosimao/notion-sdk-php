<?php

namespace Notion\Pages;

use DateTimeImmutable;
use Notion\Common\Emoji;
use Notion\Common\File;
use Notion\Pages\Properties\Factory;
use Notion\Pages\Properties\Title;

class Page
{
    private string $id;
    private DateTimeImmutable $createdTime;
    private DateTimeImmutable $lastEditedTime;
    private bool $archived;
    private Emoji|File|null $icon;
    private File|null $cover;
    private array $properties;
    private PageParent $parent;
    private string $url;

    private function __construct(
        string $id,
        DateTimeImmutable $createdTime,
        DateTimeImmutable $lastEditedTime,
        bool $archived,
        Emoji|File|null $icon,
        File|null $cover,
        array $properties,
        PageParent $parent,
        string $url,
    ) {
        if ($cover !== null && $cover->isInternal()) {
            throw new \Exception("Internal cover image is not supported");
        }

        $this->id = $id;
        $this->createdTime = $createdTime;
        $this->lastEditedTime = $lastEditedTime;
        $this->archived = $archived;
        $this->icon = $icon;
        $this->cover = $cover;
        $this->properties = $properties;
        $this->parent = $parent;
        $this->url = $url;
    }

    public static function create(PageParent $parent): self
    {
        $now = new DateTimeImmutable("now");

        return new self("", $now, $now, false, null, null, [], $parent, "");
    }

    public static function fromArray(array $array): self
    {
        $icon = null;
        if (is_array($array["icon"])) {
            $icon = match($array["icon"]["type"]) {
                "emoji" => Emoji::fromArray($array["icon"]),
                "file"  => File::fromArray($array["icon"]),
            };
        }

        $cover = isset($array["cover"]) ? File::fromArray($array["cover"]) : null;

        $parent = PageParent::fromArray($array["parent"]);

        $properties = [];
        foreach ($array["properties"] as $propertyName => $propertyArray) {
            $properties[$propertyName] = Factory::fromArray($propertyArray);
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
            "id"               => $this->id,
            "created_time"     => $this->createdTime->format(DATE_ISO8601),
            "last_edited_time" => $this->lastEditedTime->format(DATE_ISO8601),
            "archived"         => $this->archived,
            "icon"             => $this->icon?->toArray(),
            "cover"            => $this->cover?->toArray(),
            "properties"       => array_map(fn($p) => $p->toArray(), $this->properties),
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

    public function archived(): bool
    {
        return $this->archived;
    }

    public function icon(): Emoji|File|null
    {
        return $this->icon;
    }

    public function cover(): File|null
    {
        return $this->cover;
    }

    public function properties(): array
    {
        return $this->properties;
    }

    public function parent(): PageParent
    {
        return $this->parent;
    }

    public function url(): string
    {
        return $this->url;
    }

    public function withArchived(bool $archived): self
    {
        return new self(
            $this->id,
            $this->createdTime,
            $this->lastEditedTime,
            $archived,
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
            $this->archived,
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
            $this->archived,
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
            $this->archived,
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
            $this->archived,
            $this->icon,
            null,
            $this->properties,
            $this->parent,
            $this->url,
        );
    }

    public function withAddedProperty(string $name, $property): self
    {
        $properties = $this->properties;
        $properties[$name] = $property;

        return new self(
            $this->id,
            $this->createdTime,
            $this->lastEditedTime,
            $this->archived,
            $this->icon,
            $this->cover,
            $properties,
            $this->parent,
            $this->url,
        );
    }

    public function withProperties(array $properties): self
    {
        return new self(
            $this->id,
            $this->createdTime,
            $this->lastEditedTime,
            $this->archived,
            $this->icon,
            $this->cover,
            $properties,
            $this->parent,
            $this->url,
        );
    }

    public function withTitle(string $title): self
    {
        return $this->withAddedProperty("title", Title::create($title));
    }

    public function title(): Title|null
    {
        return $this->properties["title"] ?? null;
    }

    public function withParent(PageParent $parent): self
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
