<?php

namespace Notion\Views\Configuration;

use Notion\Views\ViewType;

/**
 * @psalm-import-type PropertyConfigurationJson from PropertyConfiguration
 *
 * @psalm-type MapConfigurationJson = array{
 *      type: "map",
 *      height?: string|null,
 *      map_by?: string|null,
 *      map_by_property_name?: string,
 *      properties?: PropertyConfigurationJson[]|null,
 * }
 *
 * @psalm-immutable
 */
final readonly class MapConfiguration implements ViewConfiguration
{
    /** @param PropertyConfiguration[] $properties */
    private function __construct(
        public ViewHeight|null $height,
        public string|null $mapBy,
        public string|null $mapByPropertyName,
        public array $properties,
    ) {
    }

    public static function create(): self
    {
        return new self(null, null, null, []);
    }

    /**
     * @param MapConfigurationJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        return new self(
            isset($array["height"]) ? ViewHeight::from($array["height"]) : null,
            $array["map_by"] ?? null,
            $array["map_by_property_name"] ?? null,
            array_map(PropertyConfiguration::fromArray(...), $array["properties"] ?? []),
        );
    }

    public function type(): ViewType
    {
        return ViewType::Map;
    }

    public function toArray(): array
    {
        return [
            "type"       => $this->type()->value,
            "height"     => $this->height?->value,
            "map_by"     => $this->mapBy,
            "properties" => PropertyConfigurations::toArray($this->properties),
        ];
    }

    public function changeHeight(ViewHeight $height): self
    {
        return new self($height, $this->mapBy, $this->mapByPropertyName, $this->properties);
    }

    /** @param string $propertyId Place property used to position items on the map */
    public function changeMapBy(string $propertyId): self
    {
        return new self($this->height, $propertyId, null, $this->properties);
    }

    /** Replace all property configurations */
    public function changeProperties(PropertyConfiguration ...$properties): self
    {
        return new self($this->height, $this->mapBy, $this->mapByPropertyName, $properties);
    }

    /** Add a property configuration, replacing any existing one for the same property */
    public function addProperty(PropertyConfiguration $property): self
    {
        return new self(
            $this->height,
            $this->mapBy,
            $this->mapByPropertyName,
            PropertyConfigurations::add($this->properties, $property),
        );
    }
}
