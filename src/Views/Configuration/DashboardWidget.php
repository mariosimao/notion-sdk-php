<?php

namespace Notion\Views\Configuration;

/**
 * @psalm-type DashboardWidgetJson = array{
 *      id: string,
 *      view_id: string,
 *      width?: int|null,
 *      row_index: int,
 * }
 *
 * @psalm-immutable
 */
final readonly class DashboardWidget
{
    private function __construct(
        public string $id,
        public string $viewId,
        public int|null $width,
        public int $rowIndex,
    ) {
    }

    /**
     * @param DashboardWidgetJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        return new self($array["id"], $array["view_id"], $array["width"] ?? null, $array["row_index"]);
    }

    public function toArray(): array
    {
        return [
            "id"        => $this->id,
            "view_id"   => $this->viewId,
            "width"     => $this->width,
            "row_index" => $this->rowIndex,
        ];
    }
}
