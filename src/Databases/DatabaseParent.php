<?php

namespace Notion\Databases;

/**
 * @psalm-type DatabaseParentJson = array{
 *      type: "page_id"|"workspace",
 *      page_id?: string,
 * }
 *
 * @psalm-immutable
 */
class DatabaseParent
{
    private const ALLOWED_TYPES = [ "page_id", "workspace" ];

    private string $type;
    private string|null $id;

    private function __construct(string $type, string|null $id)
    {
        if (!in_array($type, self::ALLOWED_TYPES)) {
            throw new \Exception("Invalid parent type: '{$type}'.");
        }

        $this->type = $type;
        $this->id = $id;
    }

    public static function page(string $pageId): self
    {
        return new self("page_id", $pageId);
    }

    public static function workspace(): self
    {
        return new self("workspace", null);
    }

    /**
     * @param DatabaseParentJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        $type = $array["type"];

        $id = $array["page_id"] ?? null;

        return new self($type, $id);
    }

    public function toArray(): array
    {
        $array = [];

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

    public function isPage(): bool
    {
        return $this->type === "page_id";
    }

    public function isWorkspace(): bool
    {
        return $this->type === "workspace";
    }
}
