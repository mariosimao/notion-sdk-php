<?php

namespace Notion\Test\Integration;

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
}
