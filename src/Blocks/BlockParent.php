<?php

namespace Notion\Blocks;

/**
 * @psalm-type BlockParentJson = array{
 *      type: "page_id"|"data_source_id"|"database_id"|"block_id"|"agent_id",
 *      page_id?: string,
 *      data_source_id?: string,
 *      database_id?: string,
 *      block_id?: string,
 *      agent_id?: string,
 * }
 *
 * @psalm-immutable
 */
final readonly class BlockParent
{
    private function __construct(
        public BlockParentType $type,
        public string $id,
        public string|null $databaseId = null,
    ) {
    }

    public static function page(string $pageId): self
    {
        return new self(BlockParentType::Page, $pageId);
    }

    public static function dataSource(string $dataSourceId, string|null $databaseId = null): self
    {
        return new self(BlockParentType::DataSource, $dataSourceId, $databaseId);
    }

    public static function database(string $databaseId): self
    {
        return new self(BlockParentType::Database, $databaseId);
    }

    public static function block(string $blockId): self
    {
        return new self(BlockParentType::Block, $blockId);
    }

    public static function agent(string $agentId): self
    {
        return new self(BlockParentType::Agent, $agentId);
    }

    /**
     * @psalm-param BlockParentJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        $type = BlockParentType::from($array["type"]);

        $id = $array[$type->value] ?? "";

        $databaseId = $type === BlockParentType::DataSource ? ($array["database_id"] ?? null) : null;

        return new self($type, $id, $databaseId);
    }

    public function toArray(): array
    {
        $array = [
            "type"             => $this->type->value,
            $this->type->value => $this->id,
        ];

        if ($this->isDataSource() && $this->databaseId !== null) {
            $array["database_id"] = $this->databaseId;
        }

        return $array;
    }

    public function isPage(): bool
    {
        return $this->type === BlockParentType::Page;
    }

    public function isDataSource(): bool
    {
        return $this->type === BlockParentType::DataSource;
    }

    public function isDatabase(): bool
    {
        return $this->type === BlockParentType::Database;
    }

    public function isBlock(): bool
    {
        return $this->type === BlockParentType::Block;
    }

    public function isAgent(): bool
    {
        return $this->type === BlockParentType::Agent;
    }
}
