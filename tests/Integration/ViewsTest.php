<?php

namespace Notion\Test\Integration;

use Notion\Databases\Database;
use Notion\Databases\DatabaseParent;
use Notion\DataSources\DataSource;
use Notion\DataSources\DataSourceParent;
use Notion\DataSources\Properties\Select;
use Notion\DataSources\Properties\SelectOption;
use Notion\DataSources\Query\SelectFilter;
use Notion\DataSources\Query\Sort;
use Notion\Exceptions\ApiException;
use Notion\Pages\Page;
use Notion\Pages\PageParent;
use Notion\Pages\Properties\Select as SelectProp;
use Notion\Views\Configuration\BoardConfiguration;
use Notion\Views\Configuration\GroupBy;
use Notion\Views\Configuration\TableConfiguration;
use Notion\Views\ListQuery;
use Notion\Views\QuickFilter;
use Notion\Views\View;
use Notion\Views\ViewPosition;
use Notion\Views\ViewTarget;
use Notion\Views\ViewType;
use PHPUnit\Framework\TestCase;

class ViewsTest extends TestCase
{
    public function test_list_views(): void
    {
        $client = Helper::client();
        [$database, $dataSource] = self::newDataSource();

        $viewIds = $client->views()->listAll(ListQuery::database($database->id));
        $dataSourceViewIds = $client->views()->listAll(ListQuery::dataSource($dataSource->id));

        $client->databases()->delete($database);

        $this->assertNotEmpty($viewIds);
        $this->assertNotEmpty($dataSourceViewIds);
    }

    public function test_create_find_update_and_delete_view(): void
    {
        $client = Helper::client();
        [$database, $dataSource] = self::newDataSource();
        $statusId = $dataSource->properties()->get("Status")->metadata()->id;

        $view = View::create($dataSource->id, "Board view", ViewType::Board)
            ->changeFilter(SelectFilter::property("Status")->isNotEmpty())
            ->addSort(Sort::property("Status")->descending())
            ->addQuickFilter(QuickFilter::withoutCriteria("Status"))
            ->changeConfiguration(BoardConfiguration::create(GroupBy::select($statusId)));
        $view = $client->views()->create($view, ViewTarget::database($database->id, ViewPosition::start()));

        $this->assertSame("Board view", $view->name);
        $this->assertSame(ViewType::Board, $view->type);
        $this->assertSame($dataSource->id, $view->dataSourceId);
        $this->assertNotNull($view->filter);
        $this->assertCount(1, $view->sorts);
        $this->assertCount(1, $view->quickFilters);
        $this->assertInstanceOf(BoardConfiguration::class, $view->configuration);

        $found = $client->views()->find($view->id);
        $this->assertSame($view->id, $found->id);

        $updated = $client->views()->update(
            $found->changeName("Renamed")->removeFilter()->changeSorts()->removeAllQuickFilters(),
        );
        $this->assertSame("Renamed", $updated->name);
        $this->assertNull($updated->filter);
        $this->assertSame([], $updated->sorts);
        $this->assertSame([], $updated->quickFilters);

        $client->views()->delete($updated);

        $this->expectException(ApiException::class);
        try {
            $client->views()->find($view->id);
        } finally {
            $client->databases()->delete($database);
        }
    }

    public function test_create_linked_database_view(): void
    {
        $client = Helper::client();
        [$database, $dataSource] = self::newDataSource();

        $view = View::create($dataSource->id, "Linked table", ViewType::Table)
            ->changeConfiguration(TableConfiguration::create()->changeWrapCells(true));
        $view = $client->views()->create($view, ViewTarget::linkedDatabase(Helper::testPageId()));

        $this->assertSame("Linked table", $view->name);
        $this->assertNotNull($view->parent);
        $this->assertNotSame($database->id, $view->parent->databaseId);

        $client->databases()->delete($database);
        $client->blocks()->delete($view->parent->databaseId);
    }

    public function test_query_view(): void
    {
        $client = Helper::client();
        [$database, $dataSource] = self::newDataSource();

        foreach (["Done", "Done", "To do"] as $i => $status) {
            $page = Page::create(PageParent::dataSource($dataSource->id))
                ->changeTitle("Task {$i}")
                ->addProperty("Status", SelectProp::fromName($status));
            $client->pages()->create($page);
        }

        $view = View::create($dataSource->id, "Done", ViewType::Table)
            ->changeFilter(SelectFilter::property("Status")->equals("Done"));
        $view = $client->views()->create($view, ViewTarget::database($database->id));

        $query = $client->views()->query($view, 1);
        $this->assertSame(2, $query->totalCount);
        $this->assertCount(1, $query->pageIds);
        $this->assertTrue($query->hasMore);
        $this->assertNotNull($query->nextCursor);

        $result = $client->views()->queryResults($query, $query->nextCursor, 1);
        $this->assertCount(1, $result->pageIds);

        $client->views()->deleteQuery($query);

        $pageIds = $client->views()->queryAllPages($view);
        $this->assertCount(2, $pageIds);

        $client->databases()->delete($database);
    }

    /** @return array{0: Database, 1: DataSource} */
    private static function newDataSource(): array
    {
        $client = Helper::client();

        $database = Database::create(DatabaseParent::page(Helper::testPageId()))
            ->changeTitle("Views test database");
        $database = $client->databases()->create($database);

        $dataSource = $client->dataSources()->find($database->dataSources[0]->id);
        $dataSource = $dataSource->addProperty(
            Select::create("Status")->addOption(SelectOption::fromName("Done"))
                ->addOption(SelectOption::fromName("To do")),
        );
        $dataSource = $client->dataSources()->update($dataSource);

        return [$database, $dataSource];
    }
}
