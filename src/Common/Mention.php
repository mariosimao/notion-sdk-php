<?php

namespace Notion\Common;

use Notion\Users\User;

class Mention
{
    private const ALLOWED_TYPES = [ "page", "database", "user", "date" ];

    private string $type;
    private string|null $pageId;
    private string|null $databaseId;
    private User|null $user;
    private Date|null $date;

    private function __construct(
        string $type,
        string|null $pageId,
        string|null $databaseId,
        User|null $user,
        Date|null $date,
    ) {
        if (!in_array($type, self::ALLOWED_TYPES)) {
            throw new \Exception("Invalid mention type: '{$type}'.");
        }

        $this->type = $type;
        $this->pageId = $pageId;
        $this->databaseId = $databaseId;
        $this->user = $user;
        $this->date = $date;
    }

    public static function fromArray(array $array): self
    {
        $type = $array["type"];

        $pageId = $type === "page" ? $array["page"]["id"] : null;
        $databaseId = $type === "database" ? $array["database"]["id"] : null;
        $user = $type === "user" ? User::fromArray($array["user"]) : null;
        $date = $type === "date" ? Date::fromArray($array["date"]) : null;

        return new self($type, $pageId, $databaseId, $user, $date);
    }

    public function toArray(): array
    {
        $array = [ "type" => $this->type ];

        if ($this->type === "page") {
            $array["page"] = [ "id" => $this->pageId ];
        }
        if ($this->type === "database") {
            $array["database"] = [ "id" => $this->databaseId ];
        }
        if ($this->type === "user") {
            $array["user"] = $this->user->toArray();
        }
        if ($this->type === "date") {
            $array["date"] = $this->date->toArray();
        }

        return $array;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function pageId(): string|null
    {
        return $this->pageId;
    }

    public function databaseId(): string|null
    {
        return $this->databaseId;
    }

    public function user(): User|null
    {
        return $this->user;
    }

    public function date(): Date|null
    {
        return $this->date;
    }

    public function isPage(): bool
    {
        return $this->type === "page";
    }

    public function isDatabase(): bool
    {
        return $this->type === "database";
    }

    public function isUser(): bool
    {
        return $this->type === "user";
    }

    public function isDate(): bool
    {
        return $this->type === "date";
    }
}
