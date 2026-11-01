<?php

namespace Notion\Views\Configuration;

use Notion\Views\ViewType;

/**
 * @psalm-import-type PropertyConfigurationJson from PropertyConfiguration
 * @psalm-import-type TimelinePreferenceJson from TimelinePreference
 *
 * @psalm-type TimelineConfigurationJson = array{
 *      type: "timeline",
 *      date_property_id: string,
 *      date_property_name?: string,
 *      end_date_property_id?: string|null,
 *      end_date_property_name?: string,
 *      properties?: PropertyConfigurationJson[]|null,
 *      show_table?: bool|null,
 *      table_properties?: PropertyConfigurationJson[]|null,
 *      preference?: TimelinePreferenceJson|null,
 *      arrows_by?: array{ property_id?: string|null }|null,
 *      color_by?: bool|null,
 * }
 *
 * @psalm-immutable
 */
final readonly class TimelineConfiguration implements ViewConfiguration
{
    /**
     * @param PropertyConfiguration[] $properties
     * @param PropertyConfiguration[] $tableProperties
     */
    private function __construct(
        public string $datePropertyId,
        public string|null $datePropertyName,
        public string|null $endDatePropertyId,
        public string|null $endDatePropertyName,
        public array $properties,
        public bool|null $showTable,
        public array $tableProperties,
        public TimelinePreference|null $preference,
        public string|null $arrowsByPropertyId,
        public bool|null $colorBy,
    ) {
    }

    /** @param string $datePropertyId Start date property of timeline items */
    public static function create(string $datePropertyId): self
    {
        return new self($datePropertyId, null, null, null, [], null, [], null, null, null);
    }

    /**
     * @param TimelineConfigurationJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        return new self(
            $array["date_property_id"],
            $array["date_property_name"] ?? null,
            $array["end_date_property_id"] ?? null,
            $array["end_date_property_name"] ?? null,
            array_map(PropertyConfiguration::fromArray(...), $array["properties"] ?? []),
            $array["show_table"] ?? null,
            array_map(PropertyConfiguration::fromArray(...), $array["table_properties"] ?? []),
            isset($array["preference"]) ? TimelinePreference::fromArray($array["preference"]) : null,
            $array["arrows_by"]["property_id"] ?? null,
            $array["color_by"] ?? null,
        );
    }

    public function type(): ViewType
    {
        return ViewType::Timeline;
    }

    public function toArray(): array
    {
        return [
            "type"                 => $this->type()->value,
            "date_property_id"     => $this->datePropertyId,
            "end_date_property_id" => $this->endDatePropertyId,
            "properties"           => PropertyConfigurations::toArray($this->properties),
            "show_table"           => $this->showTable,
            "table_properties"     => PropertyConfigurations::toArray($this->tableProperties),
            "preference"           => $this->preference?->toArray(),
            "arrows_by"            => $this->arrowsByPropertyId === null
                ? null
                : [ "property_id" => $this->arrowsByPropertyId ],
            "color_by"             => $this->colorBy,
        ];
    }

    public function changeDatePropertyId(string $datePropertyId): self
    {
        return $this->with(datePropertyId: $datePropertyId, datePropertyName: null);
    }

    public function changeEndDatePropertyId(string $endDatePropertyId): self
    {
        return $this->with(endDatePropertyId: $endDatePropertyId, endDatePropertyName: null);
    }

    public function removeEndDatePropertyId(): self
    {
        return $this->with(endDatePropertyId: null, endDatePropertyName: null);
    }

    /** Replace all property configurations */
    public function changeProperties(PropertyConfiguration ...$properties): self
    {
        return $this->with(properties: $properties);
    }

    /** Add a property configuration, replacing any existing one for the same property */
    public function addProperty(PropertyConfiguration $property): self
    {
        return $this->with(properties: PropertyConfigurations::add($this->properties, $property));
    }

    public function changeShowTable(bool $showTable): self
    {
        return $this->with(showTable: $showTable);
    }

    /** Replace all property configurations of the table panel */
    public function changeTableProperties(PropertyConfiguration ...$tableProperties): self
    {
        return $this->with(tableProperties: $tableProperties);
    }

    public function changePreference(TimelinePreference $preference): self
    {
        return $this->with(preference: $preference);
    }

    /** @param string $propertyId Relation property used for dependency arrows */
    public function changeArrowsBy(string $propertyId): self
    {
        return $this->with(arrowsByPropertyId: $propertyId);
    }

    public function removeArrows(): self
    {
        return $this->with(arrowsByPropertyId: null);
    }

    public function changeColorBy(bool $colorBy): self
    {
        return $this->with(colorBy: $colorBy);
    }

    /** @psalm-suppress MixedArgument */
    private function with(mixed ...$changes): self
    {
        return new self(...[ ...get_object_vars($this), ...$changes ]);
    }
}
