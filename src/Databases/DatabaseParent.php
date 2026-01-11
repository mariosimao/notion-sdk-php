<?php

namespace Notion\Databases;

/**
 * @psalm-type DatabaseParentJson = array{
 *      type: "page_id"|"workspace"|"block_id",
 *      page_id?: string,
 *      workspace?: true,
 *      block_id?: string,
 *      data_source_id?: string,
 *      database_id?: string,
 * }
 *
 * @psalm-immutable
 */
class DatabaseParent
{
    private function __construct(
        public readonly DatabaseParentType $type,
        public readonly string|null $id,
        public readonly string|null $databaseId = null,
    ) {
    }

    public static function page(string $pageId): self
    {
        return new self(DatabaseParentType::Page, $pageId);
    }

    public static function workspace(): self
    {
        return new self(DatabaseParentType::Workspace, null);
    }

    public static function block(string $blockId): self
    {
        return new self(DatabaseParentType::Block, $blockId);
    }

    public static function dataSource(string $dataSourceId, string|null $databaseId = null): self
    {
        return new self(DatabaseParentType::DataSource, $dataSourceId, $databaseId);
    }

    /**
     * @param DatabaseParentJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        $type = DatabaseParentType::from($array["type"]);

        $id = $array["page_id"] ?? $array["block_id"] ?? $array["data_source_id"] ?? null;
        $databaseId = $array["database_id"] ?? null;

        return new self($type, $id, $databaseId);
    }

    public function toArray(): array
    {
        $array = [
            "type" => $this->type->value,
        ];

        if ($this->isPage()) {
            $array["page_id"] = $this->id;
        }
        if ($this->isWorkspace()) {
            $array["workspace"] = true;
        }
        if ($this->isBlock()) {
            $array["block_id"] = $this->id;
        }
        if ($this->isDataSource()) {
            $array["data_source_id"] = $this->id;
            $array["database_id"] = $this->databaseId;
        }

        return $array;
    }

    public function isPage(): bool
    {
        return $this->type === DatabaseParentType::Page;
    }

    public function isWorkspace(): bool
    {
        return $this->type === DatabaseParentType::Workspace;
    }

    public function isBlock(): bool
    {
        return $this->type === DatabaseParentType::Block;
    }

    public function isDataSource(): bool
    {
        return $this->type === DatabaseParentType::DataSource;
    }
}
