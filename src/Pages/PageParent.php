<?php

namespace Notion\Pages;

/**
 * @psalm-type PageParentJson = array{
 *      type: "page_id"|"database_id"|"workspace",
 *      page_id?: string,
 *      database_id?: string,
 *      workspace?: true,
 * }
 *
 * @psalm-immutable
 */
class PageParent
{
    private function __construct(
        public readonly PageParentType $type,
        public readonly string|null $id,
    ) {
    }

    public static function database(string $databaseId): self
    {
        return new self(PageParentType::Database, $databaseId);
    }

    public static function page(string $pageId): self
    {
        return new self(PageParentType::Page, $pageId);
    }

    public static function workspace(): self
    {
        return new self(PageParentType::Workspace, null);
    }

    /**
     * @psalm-param PageParentJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        $type = PageParentType::from($array["type"]);

        $id = $array["page_id"] ?? $array["database_id"] ?? null;

        return new self($type, $id);
    }

    public function toArray(): array
    {
        $array = [];

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

    public function isDatabase(): bool
    {
        return $this->type === PageParentType::Database;
    }

    public function isPage(): bool
    {
        return $this->type === PageParentType::Page;
    }

    public function isWorkspace(): bool
    {
        return $this->type === PageParentType::Workspace;
    }
}
