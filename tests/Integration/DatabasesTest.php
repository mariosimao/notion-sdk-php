<?php

namespace Notion\Test\Integration;

use Notion\Common\Emoji;
use Notion\Common\RichText;
use Notion\Databases\Database;
use Notion\Databases\DatabaseParent;
use Notion\DataSources\Properties\RichTextProperty;
use Notion\Exceptions\ApiException;
use PHPUnit\Framework\TestCase;

class DatabasesTest extends TestCase
{
    public function test_create_empty_database(): void
    {
        $client = Helper::client();

        $database = Database::create(DatabaseParent::page(Helper::testPageId()))
            ->changeTitle("Empty database")
            ->changeIcon(Emoji::fromString("🌻"));

        $database = $client->databases()->create($database);

        $databaseFound = $client->databases()->find($database->id);

        $this->assertEquals("Empty database", $database->title[0]->plainText);
        if ($databaseFound->icon?->isEmoji()) {
            $this->assertEquals("🌻", $databaseFound->icon->emoji?->emoji);
        }

        $client->databases()->delete($database);
    }

    public function test_create_inline_database(): void
    {
        $client = Helper::client();

        $database = Database::create(DatabaseParent::page(Helper::testPageId()))
            ->changeTitle("Inline database")
            ->enableInline();

        $database = $client->databases()->create($database);

        $databaseFound = $client->databases()->find($database->id);

        $this->assertEquals("Inline database", $database->title[0]->plainText);
        $this->assertTrue($databaseFound->isInline);

        $client->databases()->delete($database);
    }

    public function test_create_database_with_initial_properties(): void
    {
        // Arrange
        $client = Helper::client();

        $database = Database::create(DatabaseParent::page(Helper::testPageId()))
            ->changeTitle("Database with initial properties");

        // Act
        $database = $client->databases()->create($database, [
            "Description" => RichTextProperty::create("Description")
        ]);

        $databaseFound = $client->databases()->find($database->id);
        $dataSource = $client->dataSources()->find($databaseFound->dataSources[0]->id);


        // Assert
        $this->assertEquals("Database with initial properties", $databaseFound->title[0]->plainText);
        $this->assertEquals("Description", $dataSource->properties()->getRichText("Description")->metadata()->name);

        // Clean up
        $client->databases()->delete($database);
    }

    public function test_update_database(): void
    {
        $client = Helper::client();

        $database = Database::create(DatabaseParent::page(Helper::testPageId()))
            ->changeTitle("Test database");

        $database = $client->databases()->create($database);

        $database = $database
            ->changeTitle("New test database title")
            ->changeIcon(Emoji::fromString("🍀"));
        $database = $client->databases()->update($database);

        $this->assertEquals("New test database title", $database->title[0]->plainText);
        $this->assertEquals("🍀", $database->icon?->emoji?->emoji);

        $client->databases()->delete($database);
    }

    public function test_find_inexistent_database(): void
    {
        $client = Helper::client();

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage("Could not find database with ID: b30f9991-ffcb-4b72-847a-39a74e0a774b.");
        $client->databases()->find("b30f9991-ffcb-4b72-847a-39a74e0a774b");
    }

    public function test_create_change_inexistent_parent(): void
    {
        $client = Helper::client();

        $database = Database::create(DatabaseParent::page("b30f9991-ffcb-4b72-847a-39a74e0a774b"));

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage("Could not find page with ID: b30f9991-ffcb-4b72-847a-39a74e0a774b.");
        $client->databases()->create($database);
    }

    public function test_update_deleted_database(): void
    {
        $client = Helper::client();

        $database = Database::create(DatabaseParent::page(Helper::testPageId()))
            ->changeAdvancedTitle(RichText::fromString("Dummy database"));

        $database = $client->databases()->create($database);

        $client->databases()->delete($database);

        $this->expectException(ApiException::class);
        $client->databases()->update($database);
    }

    public function test_delete_inexistent(): void
    {
        $client = Helper::client();

        $database = Database::create(DatabaseParent::page(Helper::testPageId()));

        $this->expectException(ApiException::class);
        $client->databases()->delete($database);
    }
}
