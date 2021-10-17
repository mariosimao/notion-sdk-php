<?php

namespace Notion\Pages;

use DateTimeImmutable;
use Notion\Common\Emoji;
use Notion\Common\File;
use Notion\Pages\Properties\Factory;

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
}
