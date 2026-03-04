<?php

namespace Notion\Pages;

/**
 * @psalm-type PageParentJson = array{
 *      type: "page_id"|"data_source_id"|"workspace"|"block_id",
 *      page_id?: string,
 *      data_source_id?: string,
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
        public readonly string|null $databaseId = null,
    ) {
    }

    public static function dataSource(string $dataSourceId): self
    {
        return new self(PageParentType::DataSource, $dataSourceId);
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

        $id = $array["page_id"] ?? $array["data_source_id"] ?? $array["block_id"] ?? null;

        $databaseId = null;
        if (array_key_exists("database_id", $array)) {
            $databaseId = $array["database_id"];
        }

        $parent = new self($type, $id, $databaseId);
        return $parent;
    }

    public function toArray(): array
    {
        $array = [
            "type" => $this->type->value,
        ];

        if ($this->isDataSource()) {
            $array["data_source_id"] = $this->id;
            $array["database_id"] = $this->databaseId;
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

    public function isDataSource(): bool
    {
        return $this->type === PageParentType::DataSource;
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
