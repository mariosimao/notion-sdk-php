<?php

namespace Notion\Pages\PropertyItems;

use Notion\Pages\Properties\PropertyType;
use UnexpectedValueException;

/**
 * @psalm-immutable
 */
final readonly class PropertyItemList
{
    /**
     * @param list<PropertyItemInterface> $results
     */
    private function __construct(
        public array $results,
        public string|null $nextCursor,
        public bool $hasMore,
        public PropertyItemMetadata $propertyItem,
    ) {
    }

    /**
     * @param list<PropertyItemInterface> $results
     */
    public static function create(
        array $results,
        string|null $nextCursor,
        bool $hasMore,
        PropertyItemMetadata $propertyItem,
    ): self {
        return new self($results, $nextCursor, $hasMore, $propertyItem);
    }

    /**
     * @param array<string, mixed> $array
     */
    public static function fromArray(array $array): self
    {
        /** @var list<array<string, mixed>> $rawResults */
        $rawResults = isset($array["results"]) && is_array($array["results"]) ? array_values($array["results"]) : [];
        /** @var list<PropertyItemInterface> $results */
        $results = array_map(
            function (array $item): PropertyItemInterface {
                $parsed = PropertyItemFactory::fromArray($item);
                if ($parsed instanceof PropertyItemList) {
                    throw new UnexpectedValueException("Nested property item list in results is not supported.");
                }
                return $parsed;
            },
            $rawResults,
        );

        $nextCursor = isset($array["next_cursor"]) && is_string($array["next_cursor"])
            ? $array["next_cursor"]
            : null;
        $hasMore = (bool) ($array["has_more"] ?? false);

        /** @var array<string, mixed> $rawPropItem */
        $rawPropItem = isset($array["property_item"]) && is_array($array["property_item"])
            ? $array["property_item"]
            : [];

        if (empty($rawPropItem)) {
            $rawPropItem = [
                "id" => $array["id"] ?? "",
                "type" => $array["type"] ?? "unknown",
                "next_url" => $array["next_url"] ?? null,
            ];
        }

        $propertyItem = PropertyItemMetadata::fromArray($rawPropItem);

        return new self($results, $nextCursor, $hasMore, $propertyItem);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            "object" => "list",
            "type" => "property_item",
            "results" => array_map(fn(PropertyItemInterface $item) => $item->toArray(), $this->results),
            "next_cursor" => $this->nextCursor,
            "has_more" => $this->hasMore,
            "property_item" => $this->propertyItem->toArray(),
        ];
    }

    public function id(): string
    {
        return $this->propertyItem->id;
    }

    public function type(): PropertyType
    {
        return $this->propertyItem->type;
    }

    public function nextUrl(): string|null
    {
        return $this->propertyItem->nextUrl;
    }

    public function isTitle(): bool
    {
        return $this->propertyItem->type === PropertyType::Title;
    }

    public function isRichText(): bool
    {
        return $this->propertyItem->type === PropertyType::RichText;
    }

    public function isRelation(): bool
    {
        return $this->propertyItem->type === PropertyType::Relation;
    }

    public function isPeople(): bool
    {
        return $this->propertyItem->type === PropertyType::People;
    }

    public function isRollup(): bool
    {
        return $this->propertyItem->type === PropertyType::Rollup;
    }
}
