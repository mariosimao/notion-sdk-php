<?php

namespace Notion\DataSources;

/**
 * @psalm-type DataSourceParentJson = array{
 *      type: "database_id"|"data_source_id",
 *      database_id?: string,
 *      data_source_id?: string,
 * }
 *
 * @psalm-immutable
 */
class DataSourceParent
{
    private function __construct(
        public readonly DataSourceParentType $type,
        public readonly string|null $id,
        public readonly string|null $databaseId = null,
    ) {
    }

    public static function database(string $databaseId): self
    {
        return new self(DataSourceParentType::Database, $databaseId);
    }

    public static function dataSource(string $dataSourceId, string|null $databaseId = null): self
    {
        return new self(DataSourceParentType::DataSource, $dataSourceId, $databaseId);
    }

    /**
     * @param DataSourceParentJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        $type = DataSourceParentType::from($array["type"]);

        $id = $array["data_source_id"] ?? $array["database_id"] ??  null;

        $databaseId = null;
        if ($array["data_source_id"] ?? null) {
            $databaseId = $array["database_id"] ?? null;
        }

        return new self($type, $id, $databaseId);
    }

    public function toArray(): array
    {
        $array = [
            "type" => $this->type->value,
        ];

        if ($this->isDatabase()) {
            $array["database_id"] = $this->id;
        }
        if ($this->isDataSource()) {
            $array["data_source_id"] = $this->id;
            $array["database_id"] = $this->databaseId;
        }

        return $array;
    }

    public function isDatabase(): bool
    {
        return $this->type === DataSourceParentType::Database;
    }

    public function isDataSource(): bool
    {
        return $this->type === DataSourceParentType::DataSource;
    }
}
