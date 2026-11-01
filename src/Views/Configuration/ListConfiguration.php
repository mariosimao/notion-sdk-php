<?php

namespace Notion\Views\Configuration;

use Notion\Views\ViewType;

/**
 * @psalm-import-type PropertyConfigurationJson from PropertyConfiguration
 *
 * @psalm-type ListConfigurationJson = array{
 *      type: "list",
 *      properties?: PropertyConfigurationJson[]|null,
 * }
 *
 * @psalm-immutable
 */
final readonly class ListConfiguration implements ViewConfiguration
{
    /** @param PropertyConfiguration[] $properties */
    private function __construct(
        public array $properties,
    ) {
    }

    public static function create(): self
    {
        return new self([]);
    }

    /**
     * @param ListConfigurationJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        return new self(array_map(PropertyConfiguration::fromArray(...), $array["properties"] ?? []));
    }

    public function type(): ViewType
    {
        return ViewType::List;
    }

    public function toArray(): array
    {
        return [
            "type"       => $this->type()->value,
            "properties" => PropertyConfigurations::toArray($this->properties),
        ];
    }

    /** Replace all property configurations */
    public function changeProperties(PropertyConfiguration ...$properties): self
    {
        return new self($properties);
    }

    /** Add a property configuration, replacing any existing one for the same property */
    public function addProperty(PropertyConfiguration $property): self
    {
        return new self(PropertyConfigurations::add($this->properties, $property));
    }
}
