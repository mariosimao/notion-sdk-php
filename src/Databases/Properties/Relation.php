<?php

namespace Notion\Databases\Properties;

use Notion\Exceptions\RelationException;

/**
 * @psalm-type RelationJson = array{
 *      id: string,
 *      name: string,
 *      type: "relation",
 *      relation: array{
 *          database_id: string,
 *          type: string,
 *          single_property?: array<empty, empty>,
 *          dual_property?: array{
 *              synced_property_name: string,
 *              synced_property_id: string
 *          }
 *      }
 * }
 *
 * @psalm-immutable
 */
class Relation implements PropertyInterface
{
    private function __construct(
        private readonly PropertyMetadata $metadata,
        public readonly string $databaseId,
        public readonly RelationType $type,
        public readonly string|null $syncedPropertyName,
        public readonly string|null $syncedPropertyId,
    ) {
        if ($type === RelationType::DualProperty && $syncedPropertyName === null) {
            throw RelationException::emptySyncedPropertyName();
        }

        if ($type === RelationType::DualProperty && $syncedPropertyId === null) {
            throw RelationException::emptySyncedPropertyId();
        }
    }

    public static function createUnidirectional(string $propertyName, string $databaseId): self
    {
        $metadata = PropertyMetadata::create("", $propertyName, PropertyType::Relation);
        $type = RelationType::SingleProperty;

        return new self($metadata, $databaseId, $type, null, null);
    }

    public static function createBidirectional(
        string $propertyName,
        string $databaseId,
        string $syncedPropertyName,
        string $syncedPropertyId,
    ): self {
        $metadata = PropertyMetadata::create("", $propertyName, PropertyType::Relation);
        $type = RelationType::DualProperty;

        return new self($metadata, $databaseId, $type, $syncedPropertyName, $syncedPropertyId);
    }

    public function changeToUnidirectional(): self
    {
        $newType = RelationType::SingleProperty;

        return new self($this->metadata(), $this->databaseId, $newType, null, null);
    }

    public function changeToBidirectional(string $syncedPropertyName, string $syncedPropertyId): self
    {
        $newType = RelationType::DualProperty;

        return new self(
            $this->metadata(),
            $this->databaseId,
            $newType,
            $syncedPropertyName,
            $syncedPropertyId
        );
    }

    public function metadata(): PropertyMetadata
    {
        return $this->metadata;
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var RelationJson $array */
        $metadata = PropertyMetadata::fromArray($array);

        $databaseId = $array["relation"]["database_id"];
        $type = RelationType::from($array["relation"]["type"]);

        $syncedPropertyName = null;
        $syncedPropertyId = null;
        if ($type === RelationType::DualProperty) {
            $syncedPropertyName = $array["relation"]["dual_property"]["synced_property_name"] ?? null;
            $syncedPropertyId = $array["relation"]["dual_property"]["synced_property_id"] ?? null;
        }

        return new self($metadata, $databaseId, $type, $syncedPropertyName, $syncedPropertyId);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();
        $relation = [
            "database_id" => $this->databaseId,
            "type" => $this->type->value,
        ];

        if ($this->isUniderectional()) {
            $relation["single_property"] = new \stdClass();
        }

        if ($this->isBiderectional()) {
            $relation["dual_property"] = [
                "synced_property_name" => $this->syncedPropertyName,
                "synced_property_id"   => $this->syncedPropertyId,
            ];
        }

        $array["relation"] = $relation;

        return $array;
    }

    public function isUniderectional(): bool
    {
        return $this->type === RelationType::SingleProperty;
    }

    public function isBiderectional(): bool
    {
        return $this->type === RelationType::DualProperty;
    }
}
