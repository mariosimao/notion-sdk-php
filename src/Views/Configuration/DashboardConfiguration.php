<?php

namespace Notion\Views\Configuration;

use Notion\Views\ViewType;

/**
 * Read-only layout of a dashboard view. Widgets are managed by creating views
 * with `ViewTarget::dashboard()` and deleting them.
 *
 * @psalm-import-type DashboardRowJson from DashboardRow
 *
 * @psalm-type DashboardConfigurationJson = array{
 *      type: "dashboard",
 *      rows?: DashboardRowJson[],
 * }
 *
 * @psalm-immutable
 */
final readonly class DashboardConfiguration implements ViewConfiguration
{
    /** @param DashboardRow[] $rows */
    private function __construct(
        public array $rows,
    ) {
    }

    /**
     * @param DashboardConfigurationJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        return new self(array_map(DashboardRow::fromArray(...), $array["rows"] ?? []));
    }

    public function type(): ViewType
    {
        return ViewType::Dashboard;
    }

    public function toArray(): array
    {
        return [
            "type" => $this->type()->value,
            "rows" => array_map(fn (DashboardRow $r) => $r->toArray(), $this->rows),
        ];
    }
}
