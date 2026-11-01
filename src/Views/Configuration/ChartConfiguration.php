<?php

namespace Notion\Views\Configuration;

use Notion\Views\ViewType;

/**
 * @psalm-import-type GroupByJson from GroupBy
 * @psalm-import-type ChartAggregationJson from ChartAggregation
 * @psalm-import-type ReferenceLineJson from ReferenceLine
 *
 * @psalm-type ChartConfigurationJson = array{
 *      type: "chart",
 *      chart_type: string,
 *      x_axis?: GroupByJson|null,
 *      y_axis?: ChartAggregationJson|null,
 *      x_axis_property_id?: string|null,
 *      y_axis_property_id?: string|null,
 *      value?: ChartAggregationJson|null,
 *      sort?: string|null,
 *      color_theme?: string|null,
 *      height?: string|null,
 *      hide_empty_groups?: bool|null,
 *      legend_position?: string|null,
 *      show_data_labels?: bool|null,
 *      axis_labels?: string|null,
 *      grid_lines?: string|null,
 *      cumulative?: bool|null,
 *      smooth_line?: bool|null,
 *      hide_line_fill_area?: bool|null,
 *      group_style?: string|null,
 *      y_axis_min?: int|float|null,
 *      y_axis_max?: int|float|null,
 *      donut_labels?: string|null,
 *      hide_title?: bool|null,
 *      stack_by?: GroupByJson|null,
 *      reference_lines?: ReferenceLineJson[]|null,
 *      caption?: string|null,
 *      color_by_value?: bool|null,
 * }
 *
 * @psalm-immutable
 */
final readonly class ChartConfiguration implements ViewConfiguration
{
    /** @param ReferenceLine[] $referenceLines */
    private function __construct(
        public ChartType $chartType,
        public GroupBy|null $xAxis,
        public ChartAggregation|null $yAxis,
        public string|null $xAxisPropertyId,
        public string|null $yAxisPropertyId,
        public ChartAggregation|null $value,
        public ChartSort|null $sort,
        public ChartColorTheme|null $colorTheme,
        public ViewHeight|null $height,
        public bool|null $hideEmptyGroups,
        public LegendPosition|null $legendPosition,
        public bool|null $showDataLabels,
        public AxisLabels|null $axisLabels,
        public GridLines|null $gridLines,
        public bool|null $cumulative,
        public bool|null $smoothLine,
        public bool|null $hideLineFillArea,
        public ChartGroupStyle|null $groupStyle,
        public int|float|null $yAxisMin,
        public int|float|null $yAxisMax,
        public DonutLabels|null $donutLabels,
        public bool|null $hideTitle,
        public GroupBy|null $stackBy,
        public array $referenceLines,
        public string|null $caption,
        public bool|null $colorByValue,
    ) {
    }

    public static function create(ChartType $chartType): self
    {
        return new self(
            $chartType,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            [],
            null,
            null,
        );
    }

    /**
     * @param ChartConfigurationJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        return new self(
            ChartType::from($array["chart_type"]),
            isset($array["x_axis"]) ? GroupBy::fromArray($array["x_axis"]) : null,
            isset($array["y_axis"]) ? ChartAggregation::fromArray($array["y_axis"]) : null,
            $array["x_axis_property_id"] ?? null,
            $array["y_axis_property_id"] ?? null,
            isset($array["value"]) ? ChartAggregation::fromArray($array["value"]) : null,
            isset($array["sort"]) ? ChartSort::from($array["sort"]) : null,
            isset($array["color_theme"]) ? ChartColorTheme::from($array["color_theme"]) : null,
            isset($array["height"]) ? ViewHeight::from($array["height"]) : null,
            $array["hide_empty_groups"] ?? null,
            isset($array["legend_position"]) ? LegendPosition::from($array["legend_position"]) : null,
            $array["show_data_labels"] ?? null,
            isset($array["axis_labels"]) ? AxisLabels::from($array["axis_labels"]) : null,
            isset($array["grid_lines"]) ? GridLines::from($array["grid_lines"]) : null,
            $array["cumulative"] ?? null,
            $array["smooth_line"] ?? null,
            $array["hide_line_fill_area"] ?? null,
            isset($array["group_style"]) ? ChartGroupStyle::from($array["group_style"]) : null,
            $array["y_axis_min"] ?? null,
            $array["y_axis_max"] ?? null,
            isset($array["donut_labels"]) ? DonutLabels::from($array["donut_labels"]) : null,
            $array["hide_title"] ?? null,
            isset($array["stack_by"]) ? GroupBy::fromArray($array["stack_by"]) : null,
            array_map(ReferenceLine::fromArray(...), $array["reference_lines"] ?? []),
            $array["caption"] ?? null,
            $array["color_by_value"] ?? null,
        );
    }

    public function type(): ViewType
    {
        return ViewType::Chart;
    }

    public function toArray(): array
    {
        return [
            "type"                => $this->type()->value,
            "chart_type"          => $this->chartType->value,
            "x_axis"              => $this->xAxis?->toArray(),
            "y_axis"              => $this->yAxis?->toArray(),
            "x_axis_property_id"  => $this->xAxisPropertyId,
            "y_axis_property_id"  => $this->yAxisPropertyId,
            "value"               => $this->value?->toArray(),
            "sort"                => $this->sort?->value,
            "color_theme"         => $this->colorTheme?->value,
            "height"              => $this->height?->value,
            "hide_empty_groups"   => $this->hideEmptyGroups,
            "legend_position"     => $this->legendPosition?->value,
            "show_data_labels"    => $this->showDataLabels,
            "axis_labels"         => $this->axisLabels?->value,
            "grid_lines"          => $this->gridLines?->value,
            "cumulative"          => $this->cumulative,
            "smooth_line"         => $this->smoothLine,
            "hide_line_fill_area" => $this->hideLineFillArea,
            "group_style"         => $this->groupStyle?->value,
            "y_axis_min"          => $this->yAxisMin,
            "y_axis_max"          => $this->yAxisMax,
            "donut_labels"        => $this->donutLabels?->value,
            "hide_title"          => $this->hideTitle,
            "stack_by"            => $this->stackBy?->toArray(),
            "reference_lines"     => $this->referenceLines === []
                ? null
                : array_values(array_map(fn (ReferenceLine $l) => $l->toArray(), $this->referenceLines)),
            "caption"             => $this->caption,
            "color_by_value"      => $this->colorByValue,
        ];
    }

    public function changeChartType(ChartType $chartType): self
    {
        return $this->with(chartType: $chartType);
    }

    /**
     * Grouped data mode: group rows by `$xAxis` and aggregate them with `$yAxis`
     */
    public function changeGroupedData(GroupBy $xAxis, ChartAggregation $yAxis): self
    {
        return $this->with(xAxis: $xAxis, yAxis: $yAxis, xAxisPropertyId: null, yAxisPropertyId: null);
    }

    /**
     * Results mode: plot property values of each row directly
     */
    public function changeResults(string $xAxisPropertyId, string $yAxisPropertyId): self
    {
        return $this->with(
            xAxis: null,
            yAxis: null,
            xAxisPropertyId: $xAxisPropertyId,
            yAxisPropertyId: $yAxisPropertyId,
        );
    }

    /** Aggregation displayed by number charts */
    public function changeValue(ChartAggregation $value): self
    {
        return $this->with(value: $value);
    }

    public function changeSort(ChartSort $sort): self
    {
        return $this->with(sort: $sort);
    }

    public function changeColorTheme(ChartColorTheme $colorTheme): self
    {
        return $this->with(colorTheme: $colorTheme);
    }

    public function changeHeight(ViewHeight $height): self
    {
        return $this->with(height: $height);
    }

    public function changeHideEmptyGroups(bool $hideEmptyGroups): self
    {
        return $this->with(hideEmptyGroups: $hideEmptyGroups);
    }

    public function changeLegendPosition(LegendPosition $legendPosition): self
    {
        return $this->with(legendPosition: $legendPosition);
    }

    public function changeShowDataLabels(bool $showDataLabels): self
    {
        return $this->with(showDataLabels: $showDataLabels);
    }

    public function changeAxisLabels(AxisLabels $axisLabels): self
    {
        return $this->with(axisLabels: $axisLabels);
    }

    public function changeGridLines(GridLines $gridLines): self
    {
        return $this->with(gridLines: $gridLines);
    }

    /** Line charts only */
    public function changeCumulative(bool $cumulative): self
    {
        return $this->with(cumulative: $cumulative);
    }

    /** Line charts only */
    public function changeSmoothLine(bool $smoothLine): self
    {
        return $this->with(smoothLine: $smoothLine);
    }

    /** Line charts only */
    public function changeHideLineFillArea(bool $hideLineFillArea): self
    {
        return $this->with(hideLineFillArea: $hideLineFillArea);
    }

    public function changeGroupStyle(ChartGroupStyle $groupStyle): self
    {
        return $this->with(groupStyle: $groupStyle);
    }

    public function changeYAxisRange(int|float|null $min, int|float|null $max): self
    {
        return $this->with(yAxisMin: $min, yAxisMax: $max);
    }

    /** Donut charts only */
    public function changeDonutLabels(DonutLabels $donutLabels): self
    {
        return $this->with(donutLabels: $donutLabels);
    }

    /** Number charts only */
    public function changeHideTitle(bool $hideTitle): self
    {
        return $this->with(hideTitle: $hideTitle);
    }

    public function changeStackBy(GroupBy $stackBy): self
    {
        return $this->with(stackBy: $stackBy);
    }

    public function removeStackBy(): self
    {
        return $this->with(stackBy: null);
    }

    public function changeReferenceLines(ReferenceLine ...$referenceLines): self
    {
        return $this->with(referenceLines: $referenceLines);
    }

    public function addReferenceLine(ReferenceLine $referenceLine): self
    {
        return $this->with(referenceLines: [ ...$this->referenceLines, $referenceLine ]);
    }

    public function changeCaption(string $caption): self
    {
        return $this->with(caption: $caption);
    }

    public function removeCaption(): self
    {
        return $this->with(caption: null);
    }

    public function changeColorByValue(bool $colorByValue): self
    {
        return $this->with(colorByValue: $colorByValue);
    }

    /** @psalm-suppress MixedArgument */
    private function with(mixed ...$changes): self
    {
        return new self(...[ ...get_object_vars($this), ...$changes ]);
    }
}
