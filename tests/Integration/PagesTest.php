<?php

namespace Notion\Test\Integration;

use Notion\Client;
use Notion\Common\Emoji;
use Notion\Pages\Page;
use Notion\Pages\PageParent;
use PHPUnit\Framework\TestCase;

class PagesTest extends TestCase
{
    private const DEFAULT_PARENT_ID = "3f4c46dee17f43b79587094b61407a31";

    public function test_create_empty_page(): void
    {
        $token = getenv("NOTION_TOKEN");
        $client = Client::create($token);

        $page = Page::create(PageParent::page(self::DEFAULT_PARENT_ID))
            ->withTitle("Empty page")
            ->withIcon(Emoji::create("⭐"));

        $pageAfterInsert = $client->pages()->create($page);

        $pageFound = $client->pages()->find($pageAfterInsert->id());

        $this->assertEquals("Empty page", $pageAfterInsert->title()->toString());
        $this->assertEquals("⭐", $pageFound->icon()->emoji());

        $client->pages()->delete($pageAfterInsert);
    }
}
