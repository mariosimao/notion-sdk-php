<?php

namespace Notion\Pages;

class PageParent
{
    private const ALLOWED_TYPES = [ "page", "database", "workspace" ];

    private string $type;
    private string|null $id;

    private function __construct(string $type, string|null $id)
    {
        if (!in_array($type, self::ALLOWED_TYPES)) {
            throw new \Exception("Invalid user type: '{$type}'.");
        }

        $this->type = $type;
        $this->id = $id;
    }

    public static function database(string $databaseId): self
    {
        return new self("database", $databaseId);
    }

    public static function page(string $pageId): self
    {
        return new self("page", $pageId);
    }

    public static function workspace(): self
    {
        return new self("workspace", null);
    }

    public static function fromArray(array $array): self
    {
        $type = $array["type"];

        $id = match($type) {
            "page"      => $array["page_id"],
            "database"  => $array["database_id"],
            "workspace" => null,
        };

        return new self($type, $id);
    }

    public function toArray(): array
    {
        $array = [ "type" => $this->type ];

        if ($this->isDatabase()) {
            $array["database_id"] = $this->id;
        }
        if ($this->isPage()) {
            $array["page_id"] = $this->id;
        }
        if ($this->isWorkspace()) {
            $array["workspace"] = true;
        }

        return $array;
    }

    public function id(): string|null
    {
        return $this->id;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function isDatabase(): bool
    {
        return $this->type === "database";
    }

    public function isPage(): bool
    {
        return $this->type === "page";
    }

    public function isWorkspace(): bool
    {
        return $this->type === "workspace";
    }
}
