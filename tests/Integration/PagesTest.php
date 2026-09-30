<?php

namespace Notion\Test\Integration;

use Notion\Common\Emoji;
use Notion\Databases\Database;
use Notion\Databases\DatabaseParent;
use Notion\DataSources\DataSource;
use Notion\DataSources\DataSourceParent;
use Notion\DataSources\Properties\Relation as RelationDataSourceProperty;
use Notion\Exceptions\ApiException;
use Notion\Pages\Page;
use Notion\Pages\PageParent;
use Notion\Pages\Properties\Relation;
use Notion\Pages\PropertyItems\PropertyItemList;
use Notion\Pages\PropertyItems\TitlePropertyItem;
use PHPUnit\Framework\TestCase;

class PagesTest extends TestCase
{
    public function test_create_empty_page(): void
    {
        $client = Helper::client();

        $page = Helper::newPage()
            ->changeTitle("Empty page")
            ->changeIcon(Emoji::fromString("⭐"));

        $page = $client->pages()->create($page);

        $pageFound = $client->pages()->find($page->id);

        $this->assertEquals("Empty page", $page->title()?->toString());

        if ($pageFound->icon?->isEmoji()) {
            $this->assertEquals("⭐", $pageFound->icon->emoji?->emoji);
        }

        $client->pages()->delete($page);
    }

    public function test_find_page(): void
    {
        $client = Helper::client();

        $page = $client->pages()->find(Helper::testPageId());

        $this->assertNotNull($page->title()?->toString());
    }

    public function test_find_inexistent_page(): void
    {
        $client = Helper::client();

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage("Could not find page with ID: 60e79d42-4742-41ca-8d70-cc51660cbd3c.");
        $client->pages()->find("60e79d42-4742-41ca-8d70-cc51660cbd3c");
    }

    public function test_create_change_inexistent_parent(): void
    {
        $client = Helper::client();

        $page = Page::create(PageParent::page("60e79d42-4742-41ca-8d70-cc51660cbd3c"));

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage("Could not find page with ID: 60e79d42-4742-41ca-8d70-cc51660cbd3c.");
        $client->pages()->create($page);
    }

    public function test_update_deleted_page(): void
    {
        $client = Helper::client();

        $page = Helper::newPage()
            ->changeTitle("Page to be deleted");

        $page = $client->pages()->create($page);
        $page = $client->pages()->delete($page);

        $page = $page->changeTitle("Title after deleted");

        $this->expectException(ApiException::class);
        $client->pages()->update($page);
    }

    public function test_find_property_item_list(): void
    {
        $client = Helper::client();

        $page = Helper::newPage()
            ->changeTitle("Page with title to retrieve");

        $page = $client->pages()->create($page);

        $property = $client->pages()->findProperty($page->id, "title");

        $this->assertInstanceOf(PropertyItemList::class, $property);
        $this->assertTrue($property->isTitle());
        $this->assertNotEmpty($property->results);
        $firstItem = $property->results[0];
        $this->assertInstanceOf(TitlePropertyItem::class, $firstItem);
        $this->assertSame("Page with title to retrieve", $firstItem->title->plainText);

        $client->pages()->delete($page);
    }

    public function test_relation_has_more(): void
    {
        $client = Helper::client();

        $database = Database::create(DatabaseParent::page(Helper::testPageId()))
            ->changeTitle("Relation has_more DB");
        $database = $client->databases()->create($database);

        $tasks = DataSource::create(DataSourceParent::database($database->id))
            ->changeTitle("Tasks");
        $tasks = $client->dataSources()->create($tasks);

        $projects = DataSource::create(DataSourceParent::database($database->id))
            ->changeTitle("Projects")
            ->addProperty(RelationDataSourceProperty::createUnidirectional("Tasks", $tasks->id));
        $projects = $client->dataSources()->create($projects);

        $taskIds = [];
        for ($i = 0; $i < 26; $i++) {
            $task = Page::create(PageParent::dataSource($tasks->id))->changeTitle("Task #{$i}");
            $taskIds[] = $client->pages()->create($task)->id;
        }

        $smallProject = Page::create(PageParent::dataSource($projects->id))
            ->changeTitle("Small project")
            ->addProperty("Tasks", Relation::create($taskIds[0]));
        $smallProject = $client->pages()->create($smallProject);

        $bigProject = Page::create(PageParent::dataSource($projects->id))
            ->changeTitle("Big project")
            ->addProperty("Tasks", Relation::create(...$taskIds));
        $bigProject = $client->pages()->create($bigProject);

        $smallRelation = $client->pages()->find($smallProject->id)->properties()->getRelation("Tasks");
        $bigRelation = $client->pages()->find($bigProject->id)->properties()->getRelation("Tasks");

        $client->databases()->delete($database);

        $this->assertFalse($smallRelation->hasMore);
        $this->assertCount(1, $smallRelation->pageIds);
        $this->assertTrue($bigRelation->hasMore);
        $this->assertCount(25, $bigRelation->pageIds);
    }

    public function test_find_inexistent_property(): void
    {
        $client = Helper::client();

        $page = Helper::newPage();
        $page = $client->pages()->create($page);

        $this->expectException(ApiException::class);
        try {
            $client->pages()->findProperty($page->id, "inexistent-property-id");
        } finally {
            $client->pages()->delete($page);
        }
    }
}
