<?php

namespace Notion\Views\Configuration;

/**
 * Horizontal line drawn at a y-axis value of a chart
 *
 * @psalm-type ReferenceLineJson = array{
 *      id?: string,
 *      value: int|float,
 *      label: string,
 *      color: string,
 *      dash_style: string,
 * }
 *
 * @psalm-immutable
 */
final readonly class ReferenceLine
{
    private function __construct(
        public string|null $id,
        public int|float $value,
        public string $label,
        public ReferenceLineColor $color,
        public DashStyle $dashStyle,
    ) {
    }

    public static function create(
        int|float $value,
        string $label,
        ReferenceLineColor $color = ReferenceLineColor::Gray,
        DashStyle $dashStyle = DashStyle::Solid,
    ): self {
        return new self(null, $value, $label, $color, $dashStyle);
    }

    /**
     * @param ReferenceLineJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        return new self(
            $array["id"] ?? null,
            $array["value"],
            $array["label"],
            ReferenceLineColor::from($array["color"]),
            DashStyle::from($array["dash_style"]),
        );
    }

    public function toArray(): array
    {
        $array = [];

        if ($this->id !== null) {
            $array["id"] = $this->id;
        }

        $array["value"] = $this->value;
        $array["label"] = $this->label;
        $array["color"] = $this->color->value;
        $array["dash_style"] = $this->dashStyle->value;

        return $array;
    }
}
