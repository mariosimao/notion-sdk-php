<?php

namespace Notion\Test\Integration;

use Notion\Databases\Database;
use Notion\Databases\DatabaseParent;
use Notion\Notion;
use Notion\Pages\Page;
use Notion\Pages\PageParent;
use Notion\Search\Query;
use PHPUnit\Framework\TestCase;

class SearchTest extends TestCase
{
    private const DEFAULT_PARENT_ID = "3f4c46dee17f43b79587094b61407a31";

    public function test_search(): void
    {
        $token = getenv("NOTION_TOKEN");
        if (!$token) {
            $this->markTestSkipped("Notion token is required to run integration tests.");
        }
        $client = Notion::create($token);

        // Setup
        $pageParent = PageParent::page(self::DEFAULT_PARENT_ID);
        $databaseParent = DatabaseParent::page(self::DEFAULT_PARENT_ID);
        $page1 = Page::create($pageParent)->changeTitle("SearchTest 1");
        $page2 = Page::create($pageParent)->changeTitle("SearchTest 2");
        $page3 = Page::create($pageParent)->changeTitle("SearchTest 3");
        $database = Database::create($databaseParent)->changeTitle("SearchTest 4");

        $page1 = $client->pages()->create($page1);
        $page2 = $client->pages()->create($page2);
        $page3 = $client->pages()->create($page3);
        $database = $client->databases()->create($database);

        // It takes some time to the pages to be available on the search...
        sleep(5);

        // Test
        $query = Query::title("SearchTest");
        $result = $client->search()->search($query);

        $this->assertCount(4, $result->results);

        // Teardown
        $client->pages()->delete($page1);
        $client->pages()->delete($page2);
        $client->pages()->delete($page3);
        $client->databases()->delete($database);
    }
}
