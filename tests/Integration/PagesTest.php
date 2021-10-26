<?php

namespace Notion\Test\Integration;

use Notion\Client;
use Notion\Common\Emoji;
use Notion\NotionException;
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

        $page = $client->pages()->create($page);

        $pageFound = $client->pages()->find($page->id());

        $this->assertEquals("Empty page", $page->title()->toString());
        $this->assertEquals("⭐", $pageFound->icon()->emoji());

        $client->pages()->delete($page);
    }

    public function test_find_page(): void
    {
        $token = getenv("NOTION_TOKEN");
        $client = Client::create($token);

        $page = $client->pages()->find("3f4c46dee17f43b79587094b61407a31");

        $this->assertEquals("Integration Tests", $page->title()->toString());
        $this->assertEquals("Integration Tests", $page->properties()["title"]->toString());
    }

    public function test_find_inexistent_page(): void
    {
        $token = getenv("NOTION_TOKEN");
        $client = Client::create($token);

        $this->expectException(NotionException::class);
        $this->expectErrorMessage("Could not find page with ID: 60e79d42-4742-41ca-8d70-cc51660cbd3c.");
        $client->pages()->find("60e79d42-4742-41ca-8d70-cc51660cbd3c");
    }

    public function test_create_with_inexistent_parent(): void
    {
        $token = getenv("NOTION_TOKEN");
        $client = Client::create($token);

        $page = Page::create(PageParent::page("60e79d42-4742-41ca-8d70-cc51660cbd3c"));

        $this->expectException(NotionException::class);
        $this->expectErrorMessage("Could not find page with ID: 60e79d42-4742-41ca-8d70-cc51660cbd3c.");
        $client->pages()->create($page);
    }

    public function test_update_archived_page(): void
    {
        $token = getenv("NOTION_TOKEN");
        $client = Client::create($token);

        $page = Page::create(PageParent::page(self::DEFAULT_PARENT_ID))
            ->withTitle("Deleted page");

        $page = $client->pages()->create($page);
        $page = $client->pages()->delete($page);

        $page = $page->withTitle("Title after deleted");

        $this->expectException(NotionException::class);
        $client->pages()->update($page);
    }
}
