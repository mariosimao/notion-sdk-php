<?php

namespace Notion\Test\Unit\Databases;

use Exception;
use Notion\Common\Date;
use Notion\Common\Emoji;
use Notion\Common\File;
use Notion\Common\Icon;
use Notion\Common\RichText;
use Notion\Databases\Database;
use Notion\Databases\DatabaseParent;
use Notion\Databases\Properties\Number;
use Notion\Databases\Properties\NumberFormat;
use Notion\Databases\Properties\Title;
use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase
{
    public function test_create_database(): void
    {
        $parent = DatabaseParent::page("1ce62b6f-b7f3-4201-afd0-08acb02e61c6");
        $database = Database::create($parent);

        $this->assertEquals("1ce62b6f-b7f3-4201-afd0-08acb02e61c6", $database->parent->id);
        $this->assertCount(1, $database->properties); // Title property
    }

    public function test_add_title(): void
    {
        $parent = DatabaseParent::page("1ce62b6f-b7f3-4201-afd0-08acb02e61c6");
        $database = Database::create($parent)->changeTitle("Database title");

        $this->assertEquals("Database title", RichText::multipleToString(...$database->title));
    }

    public function test_add_advanced_title(): void
    {
        $parent = DatabaseParent::page("1ce62b6f-b7f3-4201-afd0-08acb02e61c6");
        $database = Database::create($parent)->changeAdvancedTitle(
            RichText::fromString("Database title")
        );

        $this->assertEquals("Database title", $database->title[0]->plainText);
    }

    public function test_add_icon(): void
    {
        $parent = DatabaseParent::page("1ce62b6f-b7f3-4201-afd0-08acb02e61c6");
        $database = Database::create($parent)->changeIcon(
            Icon::fromEmoji(Emoji::fromString("⭐"))
        );

        if ($database->icon?->isEmoji()) {
            $this->assertEquals("⭐", $database->icon->emoji?->emoji);
        }
        $this->assertTrue($database->hasIcon());
    }

    public function test_add_file_icon(): void
    {
        $parent = DatabaseParent::page("1ce62b6f-b7f3-4201-afd0-08acb02e61c6");
        $database = Database::create($parent)->changeIcon(
            File::createExternal("http://example.com/icon.png")
        );

        $this->assertTrue($database->icon?->isFile());
    }

    public function test_remove_icon(): void
    {
        $parent = DatabaseParent::page("1ce62b6f-b7f3-4201-afd0-08acb02e61c6");
        $database = Database::create($parent)
            ->changeIcon(Icon::fromEmoji(Emoji::fromString("⭐")))
            ->removeIcon();

        $this->assertNull($database->icon);
        $this->assertFalse($database->hasIcon());
    }

    public function test_add_cover(): void
    {
        $parent = DatabaseParent::page("1ce62b6f-b7f3-4201-afd0-08acb02e61c6");
        $coverImage = File::createExternal("https://my-site.com/image.png");
        $database = Database::create($parent)->changeCover($coverImage);

        $this->assertEquals($coverImage, $database->cover);
    }

    public function test_remove_cover(): void
    {
        $parent = DatabaseParent::page("1ce62b6f-b7f3-4201-afd0-08acb02e61c6");
        $coverImage = File::createExternal("https://my-site.com/image.png");
        $database = Database::create($parent)->changeCover($coverImage)->removeCover();

        $this->assertNull($database->cover);
    }

    public function test_move_page(): void
    {
        $oldParent = DatabaseParent::page("1ce62b6f-b7f3-4201-afd0-08acb02e61c6");
        $newParent = DatabaseParent::page("08da99e5-f11d-4d26-827d-112a3a9bd07d");
        $database = Database::create($oldParent)->changeParent($newParent);

        $this->assertSame($newParent, $database->parent);
    }

    public function test_error_change_internal_cover_image(): void
    {
        $parent = DatabaseParent::page("1ce62b6f-b7f3-4201-afd0-08acb02e61c6");
        $cover = FIle::createInternal("https://notion.so/image.png");

        $this->expectException(\Exception::class);
        /** @psalm-suppress UnusedMethodCall */
        Database::create($parent)->changeCover($cover);
    }

    public function test_replace_properties(): void
    {
        $parent = DatabaseParent::page("1ce62b6f-b7f3-4201-afd0-08acb02e61c6");
        $properties = [
            "Dummy prop name" => Title::create("Dummy prop name")
        ];
        $database = Database::create($parent)->changeProperties($properties);

        $this->assertCount(1, $database->properties);
    }

    public function test_add_property(): void
    {
        $prop = Number::create("Price");

        $parent = DatabaseParent::page("1ce62b6f-b7f3-4201-afd0-08acb02e61c6");
        $database = Database::create($parent)
            ->addProperty($prop);

        $this->assertSame($prop, $database->properties()->get("Price"));
    }

    public function test_change_property(): void
    {
        $parent = DatabaseParent::page("1ce62b6f-b7f3-4201-afd0-08acb02e61c6");
        $database = Database::create($parent)->addProperty(Number::create("Price"));

        $prop = $database->properties()->getNumber("Price")->changeFormat(NumberFormat::Dollar);

        $database = $database->changeProperty($prop);

        $this->assertSame(
            NumberFormat::Dollar,
            $database->properties()->getNumber("Price")->format
        );
    }

    public function test_remove_property(): void
    {
        $parent = DatabaseParent::page("1ce62b6f-b7f3-4201-afd0-08acb02e61c6");
        $database = Database::create($parent)->addProperty(Number::create("Price"));

        $database = $database->removePropertyByName("Price");

        $this->expectException(Exception::class);
        /** @psalm-suppress UnusedMethodCall */
        $database->properties()->get("Price");
    }

    public function test_array_conversion(): void
    {
        $array = [
            "object" => "database",
            "id" => "a7e80c0b-a766-43c3-a9e9-21ce94595e0e",
            "created_time" => "2020-12-08T12:00:00.000000Z",
            "last_edited_time" => "2020-12-08T12:00:00.000000Z",
            "title" => [[
                "plain_text" => "Database title",
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
                "text" => [ "content" => "Database title" ],
            ]],
            "description" => [[
                "plain_text" => "Database description",
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
                "text" => [ "content" => "Database title" ],
            ]],
            "icon" => null,
            "cover" => null,
            "properties" => [
                "title" => [
                    "id"    => "title",
                    "name"  => "Dummy prop name",
                    "type"  => "title",
                    "title" => new \stdClass(),
                ],
            ],
            "parent" => [
                "type" => "page_id",
                "page_id" => "1ce62b6f-b7f3-4201-afd0-08acb02e61c6",
            ],
            "url" => "https://notion.so/a7e80c0ba76643c3a9e921ce94595e0e",
            "is_inline" => true,
        ];
        $database = Database::fromArray($array);

        $outArray = $array;
        unset($outArray["parent"]["type"]);

        $this->assertEquals($outArray, $database->toArray());
        $this->assertSame("a7e80c0b-a766-43c3-a9e9-21ce94595e0e", $database->id);
        $this->assertSame("https://notion.so/a7e80c0ba76643c3a9e921ce94595e0e", $database->url);
        $this->assertEquals(
            "2020-12-08T12:00:00.000000Z",
            $database->createdTime->format(Date::FORMAT),
        );
        $this->assertEquals(
            "2020-12-08T12:00:00.000000Z",
            $database->lastEditedTime->format(Date::FORMAT),
        );
        $this->assertTrue($database->isInline);
    }

    public function test_from_array_change_emoji_icon(): void
    {
        $array = [
            "object" => "database",
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
                "text" => [ "content" => "Database title" ],
            ]],
            "description" => [],
            "icon" => [
                "type" => "emoji",
                "emoji" => "⭐",
            ],
            "cover" => null,
            "properties" => [
                "Title" => [
                    "id"    => "title",
                    "name"  => "Title",
                    "type"  => "title",
                    "title" => new \stdClass(),
                ],
            ],
            "parent" => [
                "type" => "page_id",
                "page_id" => "1ce62b6f-b7f3-4201-afd0-08acb02e61c6",
            ],
            "url" => "https://notion.so/a7e80c0ba76643c3a9e921ce94595e0e",
            "is_inline" => false,
        ];
        $database = Database::fromArray($array);

        if ($database->icon?->isEmoji()) {
            $this->assertEquals("⭐", $database->icon->emoji?->emoji);
        }
    }

    public function test_from_array_change_file_icon(): void
    {
        $array = [
            "object" => "database",
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
                "text" => [ "content" => "Database title" ],
            ]],
            "description" => [],
            "icon" => [
                "type" => "external",
                "external" => [ "url" => "https://my-site.com/image.png" ],
            ],
            "cover" => null,
            "properties" => [
                "Title" => [
                    "id"    => "title",
                    "name"  => "Title",
                    "type"  => "title",
                    "title" => new \stdClass(),
                ],
            ],
            "parent" => [
                "type" => "page_id",
                "page_id" => "1ce62b6f-b7f3-4201-afd0-08acb02e61c6",
            ],
            "url" => "https://notion.so/a7e80c0ba76643c3a9e921ce94595e0e",
            "is_inline" => false,
        ];
        $database = Database::fromArray($array);

        if ($database->icon?->isFile()) {
            $this->assertEquals("https://my-site.com/image.png", $database->icon->file?->url);
        }
    }

    public function test_inline(): void
    {
        $parent = DatabaseParent::page("1ce62b6f-b7f3-4201-afd0-08acb02e61c6");
        $database = Database::create($parent);
        $this->assertFalse($database->isInline);

        $database = $database->enableInline();
        $this->assertTrue($database->isInline);

        $database = $database->disableInline();
        $this->assertFalse($database->isInline);
    }
}
