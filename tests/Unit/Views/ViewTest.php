<?php

namespace Notion\Test\Unit\Views;

use Notion\DataSources\Query\SelectFilter;
use Notion\DataSources\Query\Sort;
use Notion\Exceptions\ViewException;
use Notion\Views\Configuration\BoardConfiguration;
use Notion\Views\Configuration\DashboardConfiguration;
use Notion\Views\Configuration\GroupBy;
use Notion\Views\Configuration\TableConfiguration;
use Notion\Views\QuickFilter;
use Notion\Views\View;
use Notion\Views\ViewType;
use PHPUnit\Framework\TestCase;

/**
 * @psalm-import-type ViewJson from \Notion\Views\View
 */
class ViewTest extends TestCase
{
    public function test_create(): void
    {
        $view = View::create("ds-id", "My view", ViewType::Table);

        $this->assertSame("", $view->id);
        $this->assertSame("ds-id", $view->dataSourceId);
        $this->assertSame("My view", $view->name);
        $this->assertSame(ViewType::Table, $view->type);
        $this->assertNull($view->filter);
        $this->assertSame([], $view->sorts);
        $this->assertSame([], $view->quickFilters);
        $this->assertNull($view->configuration);
    }

    public function test_from_array(): void
    {
        $view = View::fromArray(self::viewArray());

        $this->assertSame("view-id", $view->id);
        $this->assertSame("db-id", $view->parent?->databaseId);
        $this->assertSame("ds-id", $view->dataSourceId);
        $this->assertSame(ViewType::Table, $view->type);
        $this->assertSame([ "property" => "Status", "select" => [ "equals" => "Done" ] ], $view->filter?->toArray());
        $this->assertCount(2, $view->sorts);
        $this->assertSame([ "timestamp" => "created_time", "direction" => "descending" ], $view->sorts[1]->toArray());
        $this->assertCount(2, $view->quickFilters);
        $this->assertInstanceOf(TableConfiguration::class, $view->configuration);
        $this->assertSame("user-id", $view->createdBy?->id);
        $this->assertSame("https://www.notion.so/view", $view->url);

        $array = $view->toArray();
        $original = self::viewArray();
        $this->assertSame($original["filter"] ?? null, $array["filter"]);
        $this->assertSame($original["sorts"] ?? null, $array["sorts"]);
        $this->assertEquals(
            [ "abc" => [ "select" => [ "equals" => "A" ] ], "def" => new \stdClass() ],
            $array["quick_filters"],
        );
        $this->assertSame("2025-01-01T00:00:00.000000Z", $array["created_time"]);
    }

    public function test_from_array_with_nulls(): void
    {
        $array = self::viewArray();
        $array["filter"] = null;
        $array["sorts"] = null;
        $array["quick_filters"] = null;
        $array["configuration"] = null;
        $array["data_source_id"] = null;
        $array["dashboard_view_id"] = "dashboard-id";

        $view = View::fromArray($array);

        $this->assertNull($view->filter);
        $this->assertSame([], $view->sorts);
        $this->assertSame([], $view->quickFilters);
        $this->assertNull($view->configuration);
        $this->assertNull($view->dataSourceId);
        $this->assertSame("dashboard-id", $view->dashboardViewId);
        $this->assertSame("dashboard-id", $view->toArray()["dashboard_view_id"]);
    }

    public function test_change_name_filter_and_sorts(): void
    {
        $view = View::create("ds-id", "My view", ViewType::Table)
            ->changeName("Renamed")
            ->changeFilter(SelectFilter::property("Status")->equals("Done"))
            ->changeSorts(Sort::property("Name"))
            ->addSort(Sort::createdTime()->descending());

        $this->assertSame("Renamed", $view->name);
        $this->assertNotNull($view->filter);
        $this->assertSame([
            [ "property" => "Name", "direction" => "ascending" ],
            [ "timestamp" => "created_time", "direction" => "descending" ],
        ], $view->sortsToArray());

        $this->assertNull($view->removeFilter()->filter);
    }

    public function test_quick_filters(): void
    {
        $view = View::create("ds-id", "My view", ViewType::Table)
            ->addQuickFilter(QuickFilter::withoutCriteria("Tags"))
            ->addQuickFilter(QuickFilter::fromFilter(SelectFilter::property("Status")->equals("Done")))
            ->addQuickFilter(QuickFilter::fromFilter(SelectFilter::property("Tags")->isNotEmpty()));

        $this->assertEquals([
            "Status" => [ "select" => [ "equals" => "Done" ] ],
            "Tags"   => [ "select" => [ "is_not_empty" => true ] ],
        ], $view->quickFiltersToArray());

        $view = $view->removeQuickFilter("Status");
        $this->assertCount(1, $view->quickFilters);
        $this->assertSame([ "Status" ], $view->removedQuickFilters());

        $view = $view->removeAllQuickFilters();
        $this->assertSame([], $view->quickFilters);
        $this->assertSame([ "Status", "Tags" ], $view->removedQuickFilters());

        $view = $view->addQuickFilter(QuickFilter::withoutCriteria("Tags"));
        $this->assertSame([ "Status" ], $view->removedQuickFilters());
    }

    public function test_change_configuration(): void
    {
        $view = View::create("ds-id", "Board", ViewType::Board)
            ->changeConfiguration(BoardConfiguration::create(GroupBy::select("abc")));

        $this->assertInstanceOf(BoardConfiguration::class, $view->configuration);
    }

    public function test_change_configuration_type_mismatch(): void
    {
        $this->expectException(ViewException::class);
        View::create("ds-id", "Board", ViewType::Board)->changeConfiguration(TableConfiguration::create());
    }

    public function test_dashboard_configuration_is_read_only(): void
    {
        $this->expectException(ViewException::class);
        View::create("ds-id", "Dashboard", ViewType::Dashboard)
            ->changeConfiguration(DashboardConfiguration::fromArray([ "type" => "dashboard" ]));
    }

    /** @return ViewJson */
    public static function viewArray(): array
    {
        return [
            "object"           => "view",
            "id"               => "view-id",
            "parent"           => [ "type" => "database_id", "database_id" => "db-id" ],
            "data_source_id"   => "ds-id",
            "name"             => "All tasks",
            "type"             => "table",
            "filter"           => [ "property" => "Status", "select" => [ "equals" => "Done" ] ],
            "sorts"            => [
                [ "property" => "Name", "direction" => "ascending" ],
                [ "timestamp" => "created_time", "direction" => "descending" ],
            ],
            "quick_filters"    => [
                "abc" => [ "select" => [ "equals" => "A" ] ],
                "def" => [],
            ],
            "configuration"    => [
                "type"       => "table",
                "properties" => [[ "property_id" => "title", "property_name" => "Name", "visible" => true ]],
                "wrap_cells" => false,
            ],
            "created_time"     => "2025-01-01T00:00:00.000Z",
            "last_edited_time" => "2025-01-02T00:00:00.000Z",
            "created_by"       => [ "object" => "user", "id" => "user-id" ],
            "last_edited_by"   => [ "object" => "user", "id" => "user-id" ],
            "url"              => "https://www.notion.so/view",
        ];
    }
}
