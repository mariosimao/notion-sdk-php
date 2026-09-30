<?php

namespace Notion\Test\Integration;

use Notion\Blocks\Paragraph;
use Notion\Common\Emoji;
use Notion\Exceptions\ApiException;
use Notion\Pages\Page;
use Notion\Pages\PageParent;
use Notion\Pages\PageTemplate;
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

    public function test_create_page_from_template(): void
    {
        $client = Helper::client();

        $templatePage = $client->pages()->create(
            Helper::newPage()->changeTitle("Template page"),
            [ Paragraph::fromString("Template content") ],
        );

        $page = Helper::newPage()->changeTitle("Page from template");
        $template = PageTemplate::fromId($templatePage->id, "America/New_York");
        $page = $client->pages()->create($page, template: $template);

        $this->assertNotEmpty($page->id);

        $client->pages()->delete($page);
        $client->pages()->delete($templatePage);
    }

    public function test_create_page_without_template(): void
    {
        $client = Helper::client();

        $page = Helper::newPage()->changeTitle("Page without template");
        $page = $client->pages()->create($page, template: PageTemplate::none());

        $this->assertEquals("Page without template", $page->title()?->toString());

        $client->pages()->delete($page);
    }

    public function test_apply_template_to_existing_page(): void
    {
        $client = Helper::client();

        $templatePage = $client->pages()->create(
            Helper::newPage()->changeTitle("Template page"),
            [ Paragraph::fromString("Template content") ],
        );
        $page = $client->pages()->create(
            Helper::newPage()->changeTitle("Page to apply template"),
            [ Paragraph::fromString("Content to be erased") ],
        );

        $page = $client->pages()->update(
            $page,
            template: PageTemplate::fromId($templatePage->id),
            eraseContent: true,
        );

        $this->assertEquals("Page to apply template", $page->title()?->toString());

        $client->pages()->delete($page);
        $client->pages()->delete($templatePage);
    }

    public function test_erase_page_content(): void
    {
        $client = Helper::client();

        $page = $client->pages()->create(
            Helper::newPage()->changeTitle("Page to erase"),
            [ Paragraph::fromString("Content to be erased") ],
        );

        $page = $client->pages()->update($page, eraseContent: true);

        $this->assertEmpty($client->blocks()->findChildren($page->id));

        $client->pages()->delete($page);
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
