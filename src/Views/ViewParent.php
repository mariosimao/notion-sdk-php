<?php

namespace Notion\Views;

/**
 * @psalm-type ViewParentJson = array{
 *      type: "database_id",
 *      database_id: string,
 * }
 *
 * @psalm-immutable
 */
final readonly class ViewParent
{
    private function __construct(
        public string $databaseId,
    ) {
    }

    public static function database(string $databaseId): self
    {
        return new self($databaseId);
    }

    /**
     * @param ViewParentJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        return new self($array["database_id"]);
    }

    public function toArray(): array
    {
        return [
            "type"        => "database_id",
            "database_id" => $this->databaseId,
        ];
    }
}
