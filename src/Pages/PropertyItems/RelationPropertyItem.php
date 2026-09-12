<?php

namespace Notion\Pages\PropertyItems;

use Notion\Pages\Properties\PropertyType;

/**
 * @psalm-type RelationItemJson = array{
 *      id: string,
 *      type: "relation",
 *      relation: array{ id: string },
 *      ...
 * }
 *
 * @psalm-immutable
 */
final readonly class RelationPropertyItem implements PropertyItemInterface
{
    private function __construct(
        private PropertyItemMetadata $metadata,
        public string $pageId,
    ) {
    }

    public static function create(string $pageId, string $id = ""): self
    {
        $metadata = PropertyItemMetadata::create($id, PropertyType::Relation);

        return new self($metadata, $pageId);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var RelationItemJson $array */
        $metadata = PropertyItemMetadata::fromArray($array);
        $pageId = $array["relation"]["id"];

        return new self($metadata, $pageId);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();
        $array["relation"] = ["id" => $this->pageId];

        return $array;
    }

    public function metadata(): PropertyItemMetadata
    {
        return $this->metadata;
    }
}
