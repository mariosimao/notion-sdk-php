<?php

namespace Notion\Views\Configuration;

use Notion\Views\ViewType;

/**
 * @psalm-import-type TableConfigurationJson from TableConfiguration
 * @psalm-import-type BoardConfigurationJson from BoardConfiguration
 * @psalm-import-type CalendarConfigurationJson from CalendarConfiguration
 * @psalm-import-type TimelineConfigurationJson from TimelineConfiguration
 * @psalm-import-type GalleryConfigurationJson from GalleryConfiguration
 * @psalm-import-type ListConfigurationJson from ListConfiguration
 * @psalm-import-type MapConfigurationJson from MapConfiguration
 * @psalm-import-type FormConfigurationJson from FormConfiguration
 * @psalm-import-type ChartConfigurationJson from ChartConfiguration
 * @psalm-import-type DashboardConfigurationJson from DashboardConfiguration
 *
 * @psalm-type ViewConfigurationJson = array{ type: string, ... }
 *
 * @internal
 *
 * @psalm-immutable
 */
final readonly class ViewConfigurationFactory
{
    /**
     * @param ViewConfigurationJson $array
     *
     * @psalm-suppress ArgumentTypeCoercion
     * @psalm-suppress InvalidArgument
     */
    public static function fromArray(array $array): ViewConfiguration
    {
        return match (ViewType::from($array["type"])) {
            ViewType::Table     => TableConfiguration::fromArray($array),
            ViewType::Board     => BoardConfiguration::fromArray($array),
            ViewType::Calendar  => CalendarConfiguration::fromArray($array),
            ViewType::Timeline  => TimelineConfiguration::fromArray($array),
            ViewType::Gallery   => GalleryConfiguration::fromArray($array),
            ViewType::List      => ListConfiguration::fromArray($array),
            ViewType::Map       => MapConfiguration::fromArray($array),
            ViewType::Form      => FormConfiguration::fromArray($array),
            ViewType::Chart     => ChartConfiguration::fromArray($array),
            ViewType::Dashboard => DashboardConfiguration::fromArray($array),
        };
    }
}
