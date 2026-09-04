<?php

namespace Notion\Test\Unit\DataSources;

use Exception;
use Notion\Common\Date;
use Notion\Common\Emoji;
use Notion\Common\File;
use Notion\Common\Icon;
use Notion\Common\RichText;
use Notion\DataSources\DataSource;
use Notion\DataSources\DataSourceParent;
use Notion\DataSources\Properties\Number;
use Notion\DataSources\Properties\NumberFormat;
use Notion\DataSources\Properties\Title;
use PHPUnit\Framework\TestCase;

class DataSourceTest extends TestCase
{
    public function test_create_database(): void
    {
        $parent = DataSourceParent::database("1ce62b6f-b7f3-4201-afd0-08acb02e61c6");
        $database = DataSource::create($parent);

        $this->assertEquals("1ce62b6f-b7f3-4201-afd0-08acb02e61c6", $database->parent->id);
        $this->assertCount(1, $database->properties); // Title property
    }

    public function test_add_title(): void
    {
        $parent = DataSourceParent::database("1ce62b6f-b7f3-4201-afd0-08acb02e61c6");
        $database = DataSource::create($parent)->changeTitle("DataSource title");

        $this->assertEquals("DataSource title", RichText::multipleToString(...$database->title));
    }

    public function test_add_advanced_title(): void
    {
        $parent = DataSourceParent::database("1ce62b6f-b7f3-4201-afd0-08acb02e61c6");
        $database = DataSource::create($parent)->changeAdvancedTitle(
            RichText::fromString("DataSource title")
        );

        $this->assertEquals("DataSource title", $database->title[0]->plainText);
    }

    public function test_add_icon(): void
    {
        $parent = DataSourceParent::database("1ce62b6f-b7f3-4201-afd0-08acb02e61c6");
        $database = DataSource::create($parent)->changeIcon(
            Icon::fromEmoji(Emoji::fromString("⭐"))
        );

        if ($database->icon?->isEmoji()) {
            $this->assertEquals("⭐", $database->icon->emoji?->emoji);
        }
        $this->assertTrue($database->hasIcon());
    }

    public function test_add_file_icon(): void
    {
        $parent = DataSourceParent::database("1ce62b6f-b7f3-4201-afd0-08acb02e61c6");
        $database = DataSource::create($parent)->changeIcon(
            File::createExternal("http://example.com/icon.png")
        );

        $this->assertTrue($database->icon?->isFile());
    }

    public function test_remove_icon(): void
    {
        $parent = DataSourceParent::database("1ce62b6f-b7f3-4201-afd0-08acb02e61c6");
        $database = DataSource::create($parent)
            ->changeIcon(Icon::fromEmoji(Emoji::fromString("⭐")))
            ->removeIcon();

        $this->assertNull($database->icon);
        $this->assertFalse($database->hasIcon());
    }

    public function test_add_cover(): void
    {
        $parent = DataSourceParent::database("1ce62b6f-b7f3-4201-afd0-08acb02e61c6");
        $cover = File::createExternal("https://example.com/cover.png");
        $database = DataSource::create($parent)->changeCover($cover);

        $this->assertSame($cover, $database->cover);
        $this->assertTrue($database->hasCover());
        $this->assertEquals("https://example.com/cover.png", $database->cover->url);
    }

    public function test_remove_cover(): void
    {
        $parent = DataSourceParent::database("1ce62b6f-b7f3-4201-afd0-08acb02e61c6");
        $cover = File::createExternal("https://example.com/cover.png");
        $database = DataSource::create($parent)
            ->changeCover($cover)
            ->removeCover();

        $this->assertNull($database->cover);
        $this->assertFalse($database->hasCover());
    }

    public function test_move_page(): void
    {
        $oldParent = DataSourceParent::database("1ce62b6f-b7f3-4201-afd0-08acb02e61c6");
        $newParent = DataSourceParent::database("08da99e5-f11d-4d26-827d-112a3a9bd07d");
        $database = DataSource::create($oldParent)->changeParent($newParent);

        $this->assertSame($newParent, $database->parent);
    }

    public function test_replace_properties(): void
    {
        $parent = DataSourceParent::database("1ce62b6f-b7f3-4201-afd0-08acb02e61c6");
        $properties = [
            "Dummy prop name" => Title::create("Dummy prop name")
        ];
        $database = DataSource::create($parent)->changeProperties($properties);

        $this->assertCount(1, $database->properties);
    }

    public function test_add_property(): void
    {
        $prop = Number::create("Price");

        $parent = DataSourceParent::database("1ce62b6f-b7f3-4201-afd0-08acb02e61c6");
        $database = DataSource::create($parent)
            ->addProperty($prop);

        $this->assertSame($prop, $database->properties()->get("Price"));
    }

    public function test_change_property(): void
    {
        $parent = DataSourceParent::database("1ce62b6f-b7f3-4201-afd0-08acb02e61c6");
        $database = DataSource::create($parent)->addProperty(Number::create("Price"));

        $prop = $database->properties()->getNumber("Price")->changeFormat(NumberFormat::Dollar);

        $database = $database->changeProperty($prop);

        $this->assertSame(
            NumberFormat::Dollar,
            $database->properties()->getNumber("Price")->format
        );
    }

    public function test_remove_property(): void
    {
        $parent = DataSourceParent::database("1ce62b6f-b7f3-4201-afd0-08acb02e61c6");
        $database = DataSource::create($parent)->addProperty(Number::create("Price"));

        $database = $database->removePropertyByName("Price");

        $this->expectException(Exception::class);
        /** @psalm-suppress UnusedMethodCall */
        $database->properties()->get("Price");
    }

    public function test_array_conversion(): void
    {
        $array = [
            "object" => "data_source",
            "id" => "a7e80c0b-a766-43c3-a9e9-21ce94595e0e",
            "created_time" => "2020-12-08T12:00:00.000000Z",
            "last_edited_time" => "2020-12-08T12:00:00.000000Z",
            "title" => [[
                "plain_text" => "DataSource title",
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
                "text" => [ "content" => "DataSource title" ],
            ]],
            "description" => [[
                "plain_text" => "DataSource description",
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
                "text" => [ "content" => "DataSource title" ],
            ]],
            "icon" => null,
            "cover" => [
                "type" => "external",
                "external" => [ "url" => "https://my-site.com/cover.png" ],
            ],
            "properties" => [
                "title" => [
                    "id"    => "title",
                    "name"  => "Dummy prop name",
                    "type"  => "title",
                    "title" => new \stdClass(),
                ],
            ],
            "parent" => [
                "type" => "database_id",
                "database_id" => "1ce62b6f-b7f3-4201-afd0-08acb02e61c6",
            ],
            "database_parent" => [
                "type" => "page_id",
                "page_id" => "cf735738-35e3-44aa-b3d4-aca944c8f421",
            ],
            "url" => "https://notion.so/a7e80c0ba76643c3a9e921ce94595e0e",
        ];
        $dataSource = DataSource::fromArray($array);

        $this->assertEquals($array, $dataSource->toArray());
        $this->assertSame("a7e80c0b-a766-43c3-a9e9-21ce94595e0e", $dataSource->id);
        $this->assertSame("https://notion.so/a7e80c0ba76643c3a9e921ce94595e0e", $dataSource->url);
        $this->assertEquals(
            "2020-12-08T12:00:00.000000Z",
            $dataSource->createdTime->format(Date::FORMAT),
        );
        $this->assertEquals(
            "2020-12-08T12:00:00.000000Z",
            $dataSource->lastEditedTime->format(Date::FORMAT),
        );
    }

    public function test_from_array_change_emoji_icon(): void
    {
        $array = [
            "object" => "data_source",
            "id" => "a7e80c0b-a766-43c3-a9e9-21ce94595e0e",
            "created_time" => "2020-12-08T12:00:00.000000Z",
            "last_edited_time" => "2020-12-08T12:00:00.000000Z",
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
                "text" => [ "content" => "DataSource title" ],
            ]],
            "description" => [],
            "icon" => [
                "type" => "emoji",
                "emoji" => "⭐",
            ],
            "properties" => [
                "Title" => [
                    "id"    => "title",
                    "name"  => "Title",
                    "type"  => "title",
                    "title" => new \stdClass(),
                ],
            ],
            "parent" => [
                "type" => "database_id",
                "database_id" => "1ce62b6f-b7f3-4201-afd0-08acb02e61c6",
            ],
            "url" => "https://notion.so/a7e80c0ba76643c3a9e921ce94595e0e",
        ];
        $database = DataSource::fromArray($array);

        if ($database->icon?->isEmoji()) {
            $this->assertEquals("⭐", $database->icon->emoji?->emoji);
        }
    }

    public function test_from_array_change_file_icon(): void
    {
        $array = [
            "object" => "data_source",
            "id" => "a7e80c0b-a766-43c3-a9e9-21ce94595e0e",
            "created_time" => "2020-12-08T12:00:00.000000Z",
            "last_edited_time" => "2020-12-08T12:00:00.000000Z",
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
                "text" => [ "content" => "DataSource title" ],
            ]],
            "description" => [],
            "icon" => [
                "type" => "external",
                "external" => [ "url" => "https://my-site.com/image.png" ],
            ],
            "properties" => [
                "Title" => [
                    "id"    => "title",
                    "name"  => "Title",
                    "type"  => "title",
                    "title" => new \stdClass(),
                ],
            ],
            "parent" => [
                "type" => "database_id",
                "database_id" => "1ce62b6f-b7f3-4201-afd0-08acb02e61c6",
            ],
            "url" => "https://notion.so/a7e80c0ba76643c3a9e921ce94595e0e",
        ];
        $database = DataSource::fromArray($array);

        if ($database->icon?->isFile()) {
            $this->assertEquals("https://my-site.com/image.png", $database->icon->file?->url);
        }
    }

    public function test_create_has_null_database_parent(): void
    {
        $parent = DataSourceParent::database("1ce62b6f-b7f3-4201-afd0-08acb02e61c6");
        $dataSource = DataSource::create($parent);

        $this->assertNull($dataSource->databaseParent);
    }
}
