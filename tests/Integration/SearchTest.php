<?php

namespace Notion\Test\Integration;

use Notion\Pages\Page;
use Notion\Search\Query;
use PHPUnit\Framework\TestCase;

class SearchTest extends TestCase
{
    public function test_search(): void
    {
        $client = Helper::client();

        $testPage = $client->pages()->find(Helper::testPageId());
        $title = $testPage->title()?->toString() ?? "";

        $query = Query::title($title);
        $result = $client->search()->search($query);

        $this->assertGreaterThan(0, count($result->results));
    }

    public function test_search_all(): void
    {
        $client = Helper::client();

        $query = Query::all();
        $result = $client->search()->search($query);

        $this->assertGreaterThan(0, count($result->results));
    }

    public function test_search_trash(): void
    {
        $client = Helper::client();

        $page = $client->pages()->create(Helper::newPage()->changeTitle("Search trash test"));
        $client->pages()->delete($page);

        $query = Query::title("Search trash test")->filterByTrash()->filterByPages();
        $result = $client->search()->search($query);

        foreach ($result->results as $item) {
            $this->assertInstanceOf(Page::class, $item);
            $this->assertTrue($item->inTrash);
        }
    }
}
