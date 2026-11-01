<?php

namespace Notion\Views\Configuration;

use Notion\Views\ViewType;

/**
 * @psalm-import-type PropertyConfigurationJson from PropertyConfiguration
 *
 * @psalm-type CalendarConfigurationJson = array{
 *      type: "calendar",
 *      date_property_id: string,
 *      date_property_name?: string,
 *      properties?: PropertyConfigurationJson[]|null,
 *      view_range?: string|null,
 *      show_weekends?: bool|null,
 * }
 *
 * @psalm-immutable
 */
final readonly class CalendarConfiguration implements ViewConfiguration
{
    /** @param PropertyConfiguration[] $properties */
    private function __construct(
        public string $datePropertyId,
        public string|null $datePropertyName,
        public array $properties,
        public CalendarViewRange|null $viewRange,
        public bool|null $showWeekends,
    ) {
    }

    /** @param string $datePropertyId Date property used to position items on the calendar */
    public static function create(string $datePropertyId): self
    {
        return new self($datePropertyId, null, [], null, null);
    }

    /**
     * @param CalendarConfigurationJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        return new self(
            $array["date_property_id"],
            $array["date_property_name"] ?? null,
            array_map(PropertyConfiguration::fromArray(...), $array["properties"] ?? []),
            isset($array["view_range"]) ? CalendarViewRange::from($array["view_range"]) : null,
            $array["show_weekends"] ?? null,
        );
    }

    public function type(): ViewType
    {
        return ViewType::Calendar;
    }

    public function toArray(): array
    {
        return [
            "type"             => $this->type()->value,
            "date_property_id" => $this->datePropertyId,
            "properties"       => PropertyConfigurations::toArray($this->properties),
            "view_range"       => $this->viewRange?->value,
            "show_weekends"    => $this->showWeekends,
        ];
    }

    public function changeDatePropertyId(string $datePropertyId): self
    {
        return $this->with(datePropertyId: $datePropertyId, datePropertyName: null);
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

    public function changeViewRange(CalendarViewRange $viewRange): self
    {
        return $this->with(viewRange: $viewRange);
    }

    public function changeShowWeekends(bool $showWeekends): self
    {
        return $this->with(showWeekends: $showWeekends);
    }

    /** @psalm-suppress MixedArgument */
    private function with(mixed ...$changes): self
    {
        return new self(...[ ...get_object_vars($this), ...$changes ]);
    }
}
