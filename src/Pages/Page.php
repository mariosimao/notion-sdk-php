<?php

namespace Notion\Pages;

use DateTimeImmutable;
use Notion\Common\Emoji;
use Notion\Common\File;

class Page
{
    private string $id;
    private DateTimeImmutable $createdTime;
    private DateTimeImmutable $lastEditedTime;
    private bool $archived;
    private Emoji|File $icon;
    private File|null $cover;
    private PageParent $parent;
    private string $url;

    private function __construct(
        string $id,
        DateTimeImmutable $createdTime,
        DateTimeImmutable $lastEditedTime,
        bool $archived,
        Emoji|File $icon,
        File|null $cover,
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
        $this->parent = $parent;
        $this->url = $url;
    }

    public static function fromArray(array $array): self
    {
        $icon = match($array["icon"]["type"]) {
            "emoji" => Emoji::fromArray($array["icon"]),
            "file"  => File::fromArray($array["icon"]),
        };

        $cover = isset($array["cover"]) ? File::fromArray($array["cover"]) : null;

        $parent = PageParent::fromArray($array);

        return new self(
            $array["id"],
            new DateTimeImmutable($array["created_time"]),
            new DateTimeImmutable($array["last_edited_time"]),
            $array["archived"],
            $icon,
            $cover,
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
            "icon"             => $this->icon->toArray(),
            "cover"            => $this->cover->toArray(),
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

    public function icon(): Emoji|File
    {
        return $this->icon;
    }

    public function cover(): File|null
    {
        return $this->cover;
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
