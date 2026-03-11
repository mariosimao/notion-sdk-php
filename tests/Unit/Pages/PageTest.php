<?php

namespace Notion\Test\Unit\Pages;

use Notion\Common\Date;
use Notion\Common\Emoji;
use Notion\Common\File;
use Notion\Common\Icon;
use Notion\Pages\Page;
use Notion\Pages\PageParent;
use Notion\Pages\Properties\PropertyType;
use Notion\Pages\Properties\RichTextProperty;
use Notion\Pages\Properties\Title;
use PHPUnit\Framework\TestCase;

class PageTest extends TestCase
{
    public function test_create_page(): void
    {
        $parent = PageParent::page("1ce62b6f-b7f3-4201-afd0-08acb02e61c6");
        $page = Page::create($parent);

        $this->assertEquals("1ce62b6f-b7f3-4201-afd0-08acb02e61c6", $page->parent->id);
        $this->assertEmpty($page->properties);
    }

    public function test_add_title(): void
    {
        $parent = PageParent::page("1ce62b6f-b7f3-4201-afd0-08acb02e61c6");
        $page = Page::create($parent)->changeTitle("Page title");

        $this->assertEquals("Page title", $page->title()?->toString());
    }

    public function test_add_icon(): void
    {
        $parent = PageParent::page("1ce62b6f-b7f3-4201-afd0-08acb02e61c6");
        $page = Page::create($parent)->changeIcon(Icon::fromEmoji(Emoji::fromString("⭐")));

        if ($page->icon?->isEmoji()) {
            $this->assertEquals("⭐", $page->icon->emoji?->emoji);
        }
        $this->assertTrue($page->hasIcon());
        $this->assertTrue($page->icon?->isEmoji());
    }

    public function test_add_file_icon(): void
    {
        $parent = PageParent::page("1ce62b6f-b7f3-4201-afd0-08acb02e61c6");
        $page = Page::create($parent)->changeIcon(
            File::createExternal("http://example.com/icon.png")
        );

        $this->assertTrue($page->hasIcon());
        $this->assertTrue($page->icon?->isFile());
    }

    public function test_remove_icon(): void
    {
        $parent = PageParent::page("1ce62b6f-b7f3-4201-afd0-08acb02e61c6");
        $page = Page::create($parent)
            ->changeIcon(Icon::fromEmoji(Emoji::fromString("⭐")))
            ->removeIcon();

        $this->assertFalse($page->hasIcon());
        $this->assertNull($page->icon);
    }

    public function test_add_cover(): void
    {
        $parent = PageParent::page("1ce62b6f-b7f3-4201-afd0-08acb02e61c6");
        $coverImage = File::createExternal("https://my-site.com/image.png");
        $page = Page::create($parent)->changeCover($coverImage);

        $this->assertEquals($coverImage, $page->cover);
    }

    public function test_remove_cover(): void
    {
        $parent = PageParent::page("1ce62b6f-b7f3-4201-afd0-08acb02e61c6");
        $coverImage = File::createExternal("https://my-site.com/image.png");
        $page = Page::create($parent)->changeCover($coverImage)->removeCover();

        $this->assertNull($page->cover);
    }

    public function test_archive(): void
    {
        $parent = PageParent::page("1ce62b6f-b7f3-4201-afd0-08acb02e61c6");
        $page = Page::create($parent)->archive();

        $this->assertTrue($page->archived);
    }

    public function test_unarchive(): void
    {
        $parent = PageParent::page("1ce62b6f-b7f3-4201-afd0-08acb02e61c6");
        $page = Page::create($parent)->archive()->unarchive();

        $this->assertFalse($page->archived);
    }

    public function test_move_page(): void
    {
        $oldParent = PageParent::page("1ce62b6f-b7f3-4201-afd0-08acb02e61c6");
        $newParent = PageParent::database("08da99e5-f11d-4d26-827d-112a3a9bd07d");
        $page = Page::create($oldParent)->changeParent($newParent);

        $this->assertSame($newParent, $page->parent);
    }

    public function test_add_property(): void
    {
        $page = Page::create(PageParent::workspace());

        $page = $page->addProperty("Rating", RichTextProperty::fromString("⭐⭐⭐"));

        $this->assertEquals(PropertyType::RichText, $page->getProperty("Rating")->metadata()->type);
    }

    public function test_get_property_deprecated(): void
    {
        $page = Page::create(PageParent::workspace());

        $page = $page->addProperty("Rating", RichTextProperty::fromString("⭐⭐⭐"));

        /** @psalm-suppress DeprecatedMethod */
        $this->assertEquals(PropertyType::RichText, $page->getProprety("Rating")->metadata()->type);
    }

    public function test_replace_properties(): void
    {
        $parent = PageParent::page("1ce62b6f-b7f3-4201-afd0-08acb02e61c6");
        $properties = [
            "title" => Title::fromString("Page title")
        ];
        $page = Page::create($parent)->changeProperties($properties);

        $this->assertCount(1, $page->properties);
    }

    public function test_array_conversion(): void
    {
        $array = [
            "object" => "page",
            "id" => "a7e80c0b-a766-43c3-a9e9-21ce94595e0e",
            "created_time" => "2020-12-08T12:00:00.000000Z",
            "last_edited_time" => "2020-12-08T12:00:00.000000Z",
            "archived" => false,
            "icon" => null,
            "cover" => null,
            "properties" => [
                "title" => [
                    "id" => "title",
                    "type" => "title",
                    "title" => [[
                        "plain_text" => "Page title",
                        "href" => null,
                        "annotations" => [
                            "bold"          => false,
                            "italic"        => false,
                            "strikethrough" => false,
                            "underline"     => false,
                            "code"          => false,
                            "color"         => "default",
                        ],
                        "type" => "text",
                        "text" => [ "content" => "Page title", ],
                    ]],
                ],
            ],
            "parent" => [
                "type" => "page_id",
                "page_id" => "1ce62b6f-b7f3-4201-afd0-08acb02e61c6",
            ],
            "url" => "https://notion.so/a7e80c0ba76643c3a9e921ce94595e0e",
        ];
        $page = Page::fromArray($array);

        $outArray = $array;
        unset($outArray["parent"]["type"]);

        $this->assertSame($outArray, $page->toArray());
        $this->assertSame("a7e80c0b-a766-43c3-a9e9-21ce94595e0e", $page->id);
        $this->assertSame("https://notion.so/a7e80c0ba76643c3a9e921ce94595e0e", $page->url);
        $this->assertEquals(
            "2020-12-08T12:00:00.000000Z",
            $page->createdTime->format(Date::FORMAT),
        );
        $this->assertEquals(
            "2020-12-08T12:00:00.000000Z",
            $page->lastEditedTime->format(Date::FORMAT),
        );
    }

    public function test_from_array_change_emoji_icon(): void
    {
        $array = [
            "object" => "page",
            "id" => "a7e80c0b-a766-43c3-a9e9-21ce94595e0e",
            "created_time" => "2020-12-08T12:00:00.000000Z",
            "last_edited_time" => "2020-12-08T12:00:00.000000Z",
            "archived" => false,
            "icon" => [
                "type" => "emoji",
                "emoji" => "⭐",
            ],
            "cover" => null,
            "properties" => [],
            "parent" => [
                "type" => "page_id",
                "page_id" => "1ce62b6f-b7f3-4201-afd0-08acb02e61c6",
            ],
            "url" => "https://notion.so/a7e80c0ba76643c3a9e921ce94595e0e",
        ];
        $page = Page::fromArray($array);

        if ($page->icon?->isEmoji()) {
            $this->assertEquals("⭐", $page->icon->emoji?->emoji);
        }
        $this->assertTrue($page->icon?->isEmoji());
    }

    public function test_from_array_change_file_icon(): void
    {
        $array = [
            "object" => "page",
            "id" => "a7e80c0b-a766-43c3-a9e9-21ce94595e0e",
            "created_time" => "2020-12-08T12:00:00.000000Z",
            "last_edited_time" => "2020-12-08T12:00:00.000000Z",
            "archived" => false,
            "icon" => [
                "type" => "external",
                "external" => [ "url" => "https://my-site.com/image.png" ],
            ],
            "cover" => null,
            "properties" => [],
            "parent" => [
                "type" => "page_id",
                "page_id" => "1ce62b6f-b7f3-4201-afd0-08acb02e61c6",
            ],
            "url" => "https://notion.so/a7e80c0ba76643c3a9e921ce94595e0e",
        ];
        $page = Page::fromArray($array);

        if ($page->icon?->isFile()) {
            $this->assertEquals("https://my-site.com/image.png", $page->icon->file?->url);
        }
        $this->assertTrue($page->icon?->isFile());
    }
}
