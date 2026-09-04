<?php

namespace Notion\Pages\Properties;

/**
 * @psalm-type RelationJson = array{
 *      id: string,
 *      type: "relation",
 *      relation: array{ id: non-empty-string }[],
 * }
 *
 * @psalm-immutable
 */
class Relation implements PropertyInterface
{
    /** @param string[] $pageIds */
    private function __construct(
        private readonly PropertyMetadata $metadata,
        public readonly array $pageIds
    ) {
    }

    public static function create(string ...$pageIds): self
    {
        $property = PropertyMetadata::create("", PropertyType::Relation);

        return new self($property, $pageIds);
    }

    public static function createEmpty(string $id = null): self
    {
        $property = PropertyMetadata::create($id ?? "", PropertyType::Relation);

        return new self($property, []);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var RelationJson $array */

        $property = PropertyMetadata::fromArray($array);

        $pageIds = array_map(
            function (array $pageReference): string {
                return $pageReference["id"];
            },
            $array["relation"],
        );

        return new self($property, $pageIds);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();

        $array["relation"] = array_map(
            function (string $pageId): array {
                return [ "id" => $pageId ];
            },
            $this->pageIds,
        );

        return $array;
    }

    public function metadata(): PropertyMetadata
    {
        return $this->metadata;
    }

    /** @param string[] $pageIds */
    public function changeRelations(string ...$pageIds): self
    {
        return new self($this->metadata, $pageIds);
    }

    public function addRelation(string $pageId): self
    {
        $pageIds = $this->pageIds;
        $pageIds[] = $pageId;

        return new self($this->metadata, $pageIds);
    }

    public function removeRelation(string $pageId): self
    {
        return new self(
            $this->metadata,
            array_filter($this->pageIds, fn (string $p) => $p !== $pageId),
        );
    }
}
