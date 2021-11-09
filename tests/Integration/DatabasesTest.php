<?php

namespace Notion\Test\Integration;

use Notion\Notion;
use Notion\Common\Emoji;
use Notion\Common\RichText;
use Notion\Databases\Database;
use Notion\Databases\DatabaseParent;
use Notion\Databases\Properties\CreatedBy;
use Notion\Databases\Properties\Title;
use Notion\NotionException;
use PHPUnit\Framework\TestCase;

class DatabasesTest extends TestCase
{
    private const DEFAULT_PARENT_ID = "3f4c46dee17f43b79587094b61407a31";

    public function test_create_empty_database(): void
    {
        $token = getenv("NOTION_TOKEN");
        $client = Notion::create($token);

        $database = Database::create(DatabaseParent::page(self::DEFAULT_PARENT_ID))
            ->withAdvancedTitle([ RichText::createText("Empty database") ])
            ->withIcon(Emoji::create("🌻"));

        $database = $client->databases()->create($database);

        $databaseFound = $client->databases()->find($database->id());

        $this->assertEquals("Empty database", $database->title()[0]->plainText());
        $this->assertEquals("🌻", $databaseFound->icon()->emoji());

        $client->databases()->delete($database);
    }

    public function test_find_database(): void
    {
        $token = getenv("NOTION_TOKEN");
        $client = Notion::create($token);

        $database = $client->databases()->find("a1acab7aeea2438bb0e9b23b73fb4a25");

        $this->assertEquals("Database Sample", $database->title()[0]->plainText());
    }

    public function test_update_database(): void
    {
        $token = getenv("NOTION_TOKEN");
        $client = Notion::create($token);

        $database = $client->databases()->find("a1acab7aeea2438bb0e9b23b73fb4a25");
        $database = $database->addProperty(CreatedBy::create());

        $updatedDatabase = $client->databases()->update($database);

        $this->assertEquals(
            "CreatedBy",
            $updatedDatabase->properties()["CreatedBy"]->property()->name()
        );

        // Back to original state
        $original = $updatedDatabase->withProperties([ "Title" => Title::create() ]);
        $client->databases()->update($original);
    }

    public function test_find_inexistent_database(): void
    {
        $token = getenv("NOTION_TOKEN");
        $client = Notion::create($token);

        $this->expectException(NotionException::class);
        $this->expectErrorMessage("Could not find database with ID: 60e79d42-4742-41ca-8d70-cc51660cbd3c.");
        $client->databases()->find("60e79d42-4742-41ca-8d70-cc51660cbd3c");
    }

    public function test_create_with_inexistent_parent(): void
    {
        $token = getenv("NOTION_TOKEN");
        $client = Notion::create($token);

        $database = Database::create(DatabaseParent::page("60e79d42-4742-41ca-8d70-cc51660cbd3c"));

        $this->expectException(NotionException::class);
        $this->expectErrorMessage("Could not find page with ID: 60e79d42-4742-41ca-8d70-cc51660cbd3c.");
        $client->databases()->create($database);
    }

    public function test_update_deleted_database(): void
    {
        $token = getenv("NOTION_TOKEN");
        $client = Notion::create($token);

        $database = Database::create(DatabaseParent::page(self::DEFAULT_PARENT_ID))
            ->withAdvancedTitle([ RichText::createText("Dummy database") ]);

        $database = $client->databases()->create($database);

        $client->databases()->delete($database);

        $this->expectException(NotionException::class);
        $client->databases()->update($database);
    }

    public function test_delete_database_twice(): void
    {
        $token = getenv("NOTION_TOKEN");
        $client = Notion::create($token);

        $database = Database::create(DatabaseParent::page(self::DEFAULT_PARENT_ID))
            ->withAdvancedTitle([ RichText::createText("Dummy database") ]);

        $database = $client->databases()->create($database);

        $client->databases()->delete($database);

        $this->expectException(NotionException::class);
        $client->databases()->delete($database);
    }
}
