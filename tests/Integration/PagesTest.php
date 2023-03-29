<?php

namespace Notion\Test\Integration;

use Notion\Common\Emoji;
use Notion\Exceptions\ApiException;
use Notion\Pages\Page;
use Notion\Pages\PageParent;
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

    public function test_update_archived_page(): void
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
}
