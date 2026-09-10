<?php

namespace Notion\Databases;

/**
 * @psalm-type ChildDataSourceJson = array{
 *      id: string,
 *      name: string,
 * }
 *
 * @psalm-immutable
 */
final readonly class ChildDataSource
{
    private function __construct(
        public string $id,
        public string $name,
    ) {
    }

    public static function create(string $dataSourceId, string $name): self
    {
        return new self($dataSourceId, $name);
    }

    /**
     * @param ChildDataSourceJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        return new self($array["id"], $array["name"]);
    }

    /**
     * @return ChildDataSourceJson
     */
    public function toArray(): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
        ];
    }
}
