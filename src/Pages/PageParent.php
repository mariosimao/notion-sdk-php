<?php

namespace Notion\Pages;

/**
 * @psalm-type PageParentJson = array{
 *      type: "page_id"|"database_id"|"workspace"|"block_id",
 *      page_id?: string,
 *      database_id?: string,
 *      workspace?: true,
 *      block_id?: string,
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

    public static function block(string $blockId): self
    {
        return new self(PageParentType::Block, $blockId);
    }

    /**
     * @psalm-param PageParentJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        $type = PageParentType::from($array["type"]);

        $id = $array["page_id"] ?? $array["database_id"] ?? $array["block_id"] ?? null;

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
        if ($this->isBlock()) {
            $array["block_id"] = $this->id;
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

    public function isBlock(): bool
    {
        return $this->type === PageParentType::Block;
    }
}
