<?php

namespace Notion\Pages\Properties;

/**
 * @psalm-type RelationJson = array{
 *      id: string,
 *      type: "relation",
 *      relation: list<array{ id: non-empty-string }>,
 * }
 *
 * @psalm-immutable
 */
class Relation implements PropertyInterface
{
    private const TYPE = Property::TYPE_RELATION;

    private Property $property;

    /** @var list<non-empty-string> */
    private array $pageIds;

    /** @param list<non-empty-string> $pageIds */
    private function __construct(Property $property, array $pageIds)
    {
        $this->property = $property;
        $this->pageIds = $pageIds;
    }

    /** @param list<non-empty-string> $pageIds */
    public static function create(array $pageIds): self
    {
        $property = Property::create("", self::TYPE);

        return new self($property, $pageIds);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var RelationJson $array */

        $property = Property::fromArray($array);

        $pageIds = array_map(
            function (array $pageReference): string {
                return $pageReference["id"];
            },
            $array[self::TYPE],
        );

        return new self($property, $pageIds);
    }

    public function toArray(): array
    {
        $array = $this->property->toArray();

        $array[self::TYPE] = array_map(
            function (string $pageId): array {
                return [ "id" => $pageId ];
            },
            $this->pageIds,
        );

        return $array;
    }

    public function property(): Property
    {
        return $this->property;
    }

    /** @return string[] */
    public function pageIds(): array
    {
        return $this->pageIds;
    }

    /** @param list<non-empty-string> $pageIds */
    public function withRelations(array $pageIds): self
    {
        return new self($this->property, $pageIds);
    }

    /** @param non-empty-string $pageId */
    public function addRelation(string $pageId): self
    {
        $pageIds = $this->pageIds;
        $pageIds[] = $pageId;

        return new self($this->property, $pageIds);
    }
}
