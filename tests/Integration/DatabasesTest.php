<?php

namespace Notion\Test\Integration;

use Notion\Notion;
use Notion\Common\Emoji;
use Notion\Common\RichText;
use Notion\Databases\Database;
use Notion\Databases\DatabaseParent;
use Notion\Databases\Properties\RichTextProperty;
use Notion\Databases\Query;
use Notion\Databases\Query\CompoundFilter;
use Notion\Databases\Query\DateFilter;
use Notion\Databases\Query\StatusFilter;
use Notion\Exceptions\ApiException;
use PHPUnit\Framework\TestCase;

class DatabasesTest extends TestCase
{
    private const DEFAULT_PARENT_ID = "3f4c46dee17f43b79587094b61407a31";

    public function test_create_empty_database(): void
    {
        $token = getenv("NOTION_TOKEN");
        if (!$token) {
            $this->markTestSkipped("Notion token is required to run integration tests.");
        }
        $client = Notion::create($token);

        $database = Database::create(DatabaseParent::page(self::DEFAULT_PARENT_ID))
            ->changeAdvancedTitle(RichText::fromString("Empty database"))
            ->changeIcon(Emoji::fromString("🌻"));

        $database = $client->databases()->create($database);

        $databaseFound = $client->databases()->find($database->id);

        $this->assertEquals("Empty database", $database->title[0]->plainText);
        if ($databaseFound->icon?->isEmoji()) {
            $this->assertEquals("🌻", $databaseFound->icon->emoji?->emoji);
        }

        $client->databases()->delete($database);
    }

    public function test_find_database(): void
    {
        $token = getenv("NOTION_TOKEN");
        if (!$token) {
            $this->markTestSkipped("Notion token is required to run integration tests.");
        }
        $client = Notion::create($token);

        $database = $client->databases()->find("a1acab7aeea2438bb0e9b23b73fb4a25");

        $this->assertEquals("Movies", $database->title[0]->plainText);
    }

    public function test_update_database(): void
    {
        $token = getenv("NOTION_TOKEN");
        if (!$token) {
            $this->markTestSkipped("Notion token is required to run integration tests.");
        }
        $client = Notion::create($token);

        $database = $client->databases()->find("a1acab7aeea2438bb0e9b23b73fb4a25");
        $database = $database->addProperty(RichTextProperty::create("Test"));

        $updatedDatabase = $client->databases()->update($database);

        $this->assertEquals(
            "Test",
            $updatedDatabase->properties["Test"]->metadata()->name
        );

        // Back to original state
        $original = $updatedDatabase->removePropertyByName("Test");
        $client->databases()->update($original);
    }

    public function test_find_inexistent_database(): void
    {
        $token = getenv("NOTION_TOKEN");
        if (!$token) {
            $this->markTestSkipped("Notion token is required to run integration tests.");
        }
        $client = Notion::create($token);

        $this->expectException(ApiException::class);
        $this->expectErrorMessage("Could not find database with ID: 60e79d42-4742-41ca-8d70-cc51660cbd3c.");
        $client->databases()->find("60e79d42-4742-41ca-8d70-cc51660cbd3c");
    }

    public function test_create_change_inexistent_parent(): void
    {
        $token = getenv("NOTION_TOKEN");
        if (!$token) {
            $this->markTestSkipped("Notion token is required to run integration tests.");
        }
        $client = Notion::create($token);

        $database = Database::create(DatabaseParent::page("60e79d42-4742-41ca-8d70-cc51660cbd3c"));

        $this->expectException(ApiException::class);
        $this->expectErrorMessage("Could not find page with ID: 60e79d42-4742-41ca-8d70-cc51660cbd3c.");
        $client->databases()->create($database);
    }

    public function test_update_deleted_database(): void
    {
        $token = getenv("NOTION_TOKEN");
        if (!$token) {
            $this->markTestSkipped("Notion token is required to run integration tests.");
        }
        $client = Notion::create($token);

        $database = Database::create(DatabaseParent::page(self::DEFAULT_PARENT_ID))
            ->changeAdvancedTitle(RichText::fromString("Dummy database"));

        $database = $client->databases()->create($database);

        $client->databases()->delete($database);

        $this->expectException(ApiException::class);
        $client->databases()->update($database);
    }

    public function test_delete_inexistent(): void
    {
        $token = getenv("NOTION_TOKEN");
        if (!$token) {
            $this->markTestSkipped("Notion token is required to run integration tests.");
        }
        $client = Notion::create($token);

        $database = Database::create(DatabaseParent::page(self::DEFAULT_PARENT_ID));

        $this->expectException(ApiException::class);
        $client->databases()->delete($database);
    }

    public function test_query_all_pages_from_database(): void
    {
        $token = getenv("NOTION_TOKEN");
        if (!$token) {
            $this->markTestSkipped("Notion token is required to run integration tests.");
        }
        $client = Notion::create($token);

        $database = $client->databases()->find("a1acab7aeea2438bb0e9b23b73fb4a25");

        $pages = $client->databases()->queryAllPages($database);
        $this->assertCount(7, $pages);
    }

    public function test_query_big_database(): void
    {
        $token = getenv("NOTION_TOKEN");
        if (!$token) {
            $this->markTestSkipped("Notion token is required to run integration tests.");
        }
        $client = Notion::create($token);

        $database = $client->databases()->find("7b23ad4e145c41aea5604374406c2bc0");

        $pages = $client->databases()->queryAllPages($database);
        $this->assertCount(102, $pages);
    }

    public function test_query_database(): void
    {
        $token = getenv("NOTION_TOKEN");
        if (!$token) {
            $this->markTestSkipped("Notion token is required to run integration tests.");
        }
        $client = Notion::create($token);

        $database = $client->databases()->find("a1acab7aeea2438bb0e9b23b73fb4a25");

        /**
         * 90s movies not watched
         *
         * Status != Watched AND
         * Release >= 1990-01-01 AND
         * Release <= 1999-12-31
         *
         */
        $query = Query::create()->changeFilter(
            CompoundFilter::and(
                StatusFilter::property("Status")->doesNotEqual("Watched"),
                DateFilter::property("Release date")->onOrAfter("1990-01-01"),
                DateFilter::property("Release date")->onOrBefore("1999-12-31"),
            ),
        );

        $result = $client->databases()->query($database, $query);

        $this->assertCount(2, $result->pages);
    }

    public function test_query_inexistent_database(): void
    {
        $token = getenv("NOTION_TOKEN");
        if (!$token) {
            $this->markTestSkipped("Notion token is required to run integration tests.");
        }
        $client = Notion::create($token);

        $database = Database::create(DatabaseParent::page("a-page-id"));
        $query = Query::create();

        $this->expectException(ApiException::class);
        $client->databases()->query($database, $query);
    }
}
