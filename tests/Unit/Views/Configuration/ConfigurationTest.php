<?php

namespace Notion\Test\Unit\Views\Configuration;

use Notion\Views\Configuration\BoardConfiguration;
use Notion\Views\Configuration\CalendarConfiguration;
use Notion\Views\Configuration\CalendarViewRange;
use Notion\Views\Configuration\CardLayout;
use Notion\Views\Configuration\ChartAggregation;
use Notion\Views\Configuration\ChartAggregator;
use Notion\Views\Configuration\ChartConfiguration;
use Notion\Views\Configuration\ChartSort;
use Notion\Views\Configuration\ChartType;
use Notion\Views\Configuration\Cover;
use Notion\Views\Configuration\CoverSize;
use Notion\Views\Configuration\DashboardConfiguration;
use Notion\Views\Configuration\DashStyle;
use Notion\Views\Configuration\FormConfiguration;
use Notion\Views\Configuration\GalleryConfiguration;
use Notion\Views\Configuration\GroupBy;
use Notion\Views\Configuration\ListConfiguration;
use Notion\Views\Configuration\MapConfiguration;
use Notion\Views\Configuration\PropertyConfiguration;
use Notion\Views\Configuration\ReferenceLine;
use Notion\Views\Configuration\ReferenceLineColor;
use Notion\Views\Configuration\StatusShowAs;
use Notion\Views\Configuration\SubmissionPermissions;
use Notion\Views\Configuration\SubtaskConfiguration;
use Notion\Views\Configuration\SubtaskDisplayMode;
use Notion\Views\Configuration\TableConfiguration;
use Notion\Views\Configuration\TimelineConfiguration;
use Notion\Views\Configuration\TimelinePreference;
use Notion\Views\Configuration\TimelineZoomLevel;
use Notion\Views\Configuration\ViewConfigurationFactory;
use Notion\Views\Configuration\ViewHeight;
use Notion\Views\ViewType;
use PHPUnit\Framework\TestCase;

/**
 * @psalm-suppress MixedArgumentTypeCoercion Round-trips pass `toArray()` output to `fromArray()`
 */
class ConfigurationTest extends TestCase
{
    public function test_table(): void
    {
        $config = TableConfiguration::create()
            ->addProperty(PropertyConfiguration::create("title")->changeWidth(300))
            ->addProperty(PropertyConfiguration::create("abc")->hide())
            ->addProperty(PropertyConfiguration::create("abc")->changeStatusShowAs(StatusShowAs::Checkbox))
            ->changeGroupBy(GroupBy::select("abc"))
            ->changeSubtasks(SubtaskConfiguration::create()->changeDisplayMode(SubtaskDisplayMode::Flattened))
            ->changeWrapCells(true)
            ->changeFrozenColumnIndex(1)
            ->changeShowVerticalLines(false);

        $this->assertSame(ViewType::Table, $config->type());
        $this->assertSame([
            "type"       => "table",
            "properties" => [
                [ "property_id" => "title", "width" => 300 ],
                [ "property_id" => "abc", "status_show_as" => "checkbox" ],
            ],
            "group_by"   => [ "type" => "select", "property_id" => "abc", "sort" => [ "type" => "manual" ] ],
            "subtasks"   => [ "display_mode" => "flattened" ],
            "wrap_cells" => true,
            "frozen_column_index" => 1,
            "show_vertical_lines" => false,
        ], $config->toArray());

        $this->assertEquals($config, TableConfiguration::fromArray($config->toArray()));
    }

    public function test_table_omits_non_nullable_fields(): void
    {
        $config = TableConfiguration::create()->changeGroupBy(GroupBy::select("abc"))->removeGroupBy();

        $this->assertSame([
            "type"       => "table",
            "properties" => null,
            "group_by"   => null,
            "subtasks"   => null,
        ], $config->toArray());
    }

    public function test_board(): void
    {
        $config = BoardConfiguration::create(GroupBy::select("abc"))
            ->changeSubGroupBy(GroupBy::checkbox("def"))
            ->changeCover(Cover::property("files"))
            ->changeCoverSize(CoverSize::Large)
            ->changeCardLayout(CardLayout::Compact);

        $array = $config->toArray();
        $this->assertSame("board", $array["type"]);
        $this->assertSame([ "type" => "property", "property_id" => "files" ], $array["cover"]);
        $this->assertSame("large", $array["cover_size"]);
        $this->assertNull($array["cover_aspect"]);
        $this->assertSame("compact", $array["card_layout"]);
        $this->assertEquals($config, BoardConfiguration::fromArray($config->toArray()));

        $removed = $config->removeCover()->removeSubGroupBy()->toArray();
        $this->assertNull($removed["cover"]);
        $this->assertNull($removed["cover_size"]);
        $this->assertNull($removed["sub_group_by"]);
    }

    public function test_gallery(): void
    {
        $config = GalleryConfiguration::create()->changeCover(Cover::pageContent());

        $this->assertSame(ViewType::Gallery, $config->type());
        $this->assertSame([ "type" => "page_content" ], $config->toArray()["cover"]);
        $this->assertEquals($config, GalleryConfiguration::fromArray($config->toArray()));
    }

    public function test_list(): void
    {
        $config = ListConfiguration::create()->changeProperties(PropertyConfiguration::create("title"));

        $this->assertSame([
            "type"       => "list",
            "properties" => [[ "property_id" => "title" ]],
        ], $config->toArray());
        $this->assertEquals($config, ListConfiguration::fromArray($config->toArray()));
    }

    public function test_calendar(): void
    {
        $config = CalendarConfiguration::create("date")
            ->changeViewRange(CalendarViewRange::Week)
            ->changeShowWeekends(false);

        $this->assertSame([
            "type"             => "calendar",
            "date_property_id" => "date",
            "properties"       => null,
            "view_range"       => "week",
            "show_weekends"    => false,
        ], $config->toArray());

        $fromApi = CalendarConfiguration::fromArray([
            "type"               => "calendar",
            "date_property_id"   => "date",
            "date_property_name" => "Due",
        ]);
        $this->assertSame("Due", $fromApi->datePropertyName);
        $this->assertNull($fromApi->changeDatePropertyId("other")->datePropertyName);
    }

    public function test_timeline(): void
    {
        $config = TimelineConfiguration::create("start")
            ->changeEndDatePropertyId("end")
            ->changeShowTable(true)
            ->changeTableProperties(PropertyConfiguration::create("title"))
            ->changePreference(TimelinePreference::create(TimelineZoomLevel::Month))
            ->changeArrowsBy("rel")
            ->changeColorBy(true);

        $array = $config->toArray();
        $this->assertSame("end", $array["end_date_property_id"]);
        $this->assertSame([ "property_id" => "rel" ], $array["arrows_by"]);
        $this->assertSame([ "zoom_level" => "month" ], $array["preference"]);
        $this->assertEquals($config, TimelineConfiguration::fromArray($config->toArray()));

        $this->assertNull($config->removeArrows()->toArray()["arrows_by"]);
        $this->assertNull($config->removeEndDatePropertyId()->toArray()["end_date_property_id"]);
    }

    public function test_map(): void
    {
        $config = MapConfiguration::create()->changeHeight(ViewHeight::Large)->changeMapBy("place");

        $this->assertSame([
            "type"       => "map",
            "height"     => "large",
            "map_by"     => "place",
            "properties" => null,
        ], $config->toArray());
        $this->assertEquals($config, MapConfiguration::fromArray($config->toArray()));
    }

    public function test_form(): void
    {
        $config = FormConfiguration::create()
            ->close()
            ->changeAnonymousSubmissions(true)
            ->changeSubmissionPermissions(SubmissionPermissions::Reader);

        $this->assertSame([
            "type"                   => "form",
            "is_form_closed"         => true,
            "anonymous_submissions"  => true,
            "submission_permissions" => "reader",
        ], $config->toArray());
        $this->assertFalse($config->open()->isFormClosed);
        $this->assertEquals($config, FormConfiguration::fromArray($config->toArray()));
    }

    public function test_chart(): void
    {
        $config = ChartConfiguration::create(ChartType::Bar)
            ->changeGroupedData(GroupBy::select("abc"), ChartAggregation::count())
            ->changeSort(ChartSort::YDescending)
            ->changeYAxisRange(0, 10.5)
            ->addReferenceLine(ReferenceLine::create(5, "Goal", ReferenceLineColor::Red, DashStyle::Dash))
            ->changeCaption("Caption");

        $array = $config->toArray();
        $this->assertSame("chart", $array["type"]);
        $this->assertSame("bar", $array["chart_type"]);
        $this->assertSame([ "aggregator" => "count" ], $array["y_axis"]);
        $this->assertNull($array["x_axis_property_id"]);
        $this->assertSame("y_descending", $array["sort"]);
        $this->assertSame(0, $array["y_axis_min"]);
        $this->assertSame(10.5, $array["y_axis_max"]);
        $this->assertSame([
            [ "value" => 5, "label" => "Goal", "color" => "red", "dash_style" => "dash" ],
        ], $array["reference_lines"]);
        $this->assertEquals($config, ChartConfiguration::fromArray($config->toArray()));

        $results = $config->changeResults("x", "y")->toArray();
        $this->assertNull($results["x_axis"]);
        $this->assertNull($results["y_axis"]);
        $this->assertSame("x", $results["x_axis_property_id"]);

        $number = ChartConfiguration::create(ChartType::Number)
            ->changeValue(ChartAggregation::create(ChartAggregator::Sum, "num"));
        $this->assertSame([ "aggregator" => "sum", "property_id" => "num" ], $number->toArray()["value"]);
    }

    public function test_dashboard(): void
    {
        $array = [
            "type" => "dashboard",
            "rows" => [
                [
                    "id"      => "row1",
                    "widgets" => [[ "id" => "w1", "view_id" => "v1", "width" => 6, "row_index" => 0 ]],
                    "height"  => 400,
                ],
            ],
        ];

        $config = DashboardConfiguration::fromArray($array);

        $this->assertSame(ViewType::Dashboard, $config->type());
        $this->assertSame("v1", $config->rows[0]->widgets[0]->viewId);
        $this->assertSame($array, $config->toArray());
    }

    public function test_factory(): void
    {
        $types = [
            "table"     => TableConfiguration::class,
            "list"      => ListConfiguration::class,
            "gallery"   => GalleryConfiguration::class,
            "map"       => MapConfiguration::class,
            "form"      => FormConfiguration::class,
            "dashboard" => DashboardConfiguration::class,
        ];

        foreach ($types as $type => $class) {
            $this->assertInstanceOf($class, ViewConfigurationFactory::fromArray([ "type" => $type ]));
        }

        $this->assertInstanceOf(
            BoardConfiguration::class,
            ViewConfigurationFactory::fromArray([
                "type"     => "board",
                "group_by" => [ "type" => "select", "property_id" => "abc" ],
            ]),
        );
        $this->assertInstanceOf(
            CalendarConfiguration::class,
            ViewConfigurationFactory::fromArray([ "type" => "calendar", "date_property_id" => "d" ]),
        );
        $this->assertInstanceOf(
            TimelineConfiguration::class,
            ViewConfigurationFactory::fromArray([ "type" => "timeline", "date_property_id" => "d" ]),
        );
        $this->assertInstanceOf(
            ChartConfiguration::class,
            ViewConfigurationFactory::fromArray([ "type" => "chart", "chart_type" => "donut" ]),
        );
    }
}
