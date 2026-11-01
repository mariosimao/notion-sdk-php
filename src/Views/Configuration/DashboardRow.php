<?php

namespace Notion\Views\Configuration;

/**
 * @psalm-import-type DashboardWidgetJson from DashboardWidget
 *
 * @psalm-type DashboardRowJson = array{
 *      id: string,
 *      widgets: DashboardWidgetJson[],
 *      height?: int|null,
 * }
 *
 * @psalm-immutable
 */
final readonly class DashboardRow
{
    /** @param DashboardWidget[] $widgets */
    private function __construct(
        public string $id,
        public array $widgets,
        public int|null $height,
    ) {
    }

    /**
     * @param DashboardRowJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        return new self(
            $array["id"],
            array_map(DashboardWidget::fromArray(...), $array["widgets"]),
            $array["height"] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            "id"      => $this->id,
            "widgets" => array_map(fn (DashboardWidget $w) => $w->toArray(), $this->widgets),
            "height"  => $this->height,
        ];
    }
}
