<?php

namespace Notion\Test\Integration;

use Notion\Notion;
use Notion\Common\Emoji;
use Notion\Common\RichText;
use Notion\Databases\Database;
use Notion\Databases\DatabaseParent;
use Notion\Databases\Properties\RichText as PropertiesRichText;
use Notion\Databases\Query;
use Notion\Databases\Query\CompoundFilter;
use Notion\Databases\Query\DateFilter;
use Notion\Databases\Query\SelectFilter;
use Notion\NotionException;
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
            ->withAdvancedTitle([ RichText::createText("Empty database") ])
            ->withIcon(Emoji::create("🌻"));

        $database = $client->databases()->create($database);

        $databaseFound = $client->databases()->find($database->id());

        $this->assertEquals("Empty database", $database->title()[0]->plainText());
        if ($databaseFound->iconIsEmoji()) {
            $this->assertEquals("🌻", $databaseFound->icon()->emoji());
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

        $this->assertEquals("Movies", $database->title()[0]->plainText());
    }

    public function test_update_database(): void
    {
        $token = getenv("NOTION_TOKEN");
        if (!$token) {
            $this->markTestSkipped("Notion token is required to run integration tests.");
        }
        $client = Notion::create($token);

        $database = $client->databases()->find("a1acab7aeea2438bb0e9b23b73fb4a25");
        $oldProperties = $database->properties();

        $database = $database->addProperty(PropertiesRichText::create("Test"));

        $updatedDatabase = $client->databases()->update($database);

        $this->assertEquals(
            "Test",
            $updatedDatabase->properties()["Test"]->property()->name()
        );

        // Back to original state
        $original = $updatedDatabase->withProperties($oldProperties);
        $client->databases()->update($original);
    }

    public function test_find_inexistent_database(): void
    {
        $token = getenv("NOTION_TOKEN");
        if (!$token) {
            $this->markTestSkipped("Notion token is required to run integration tests.");
        }
        $client = Notion::create($token);

        $this->expectException(NotionException::class);
        $this->expectErrorMessage("Could not find database with ID: 60e79d42-4742-41ca-8d70-cc51660cbd3c.");
        $client->databases()->find("60e79d42-4742-41ca-8d70-cc51660cbd3c");
    }

    public function test_create_with_inexistent_parent(): void
    {
        $token = getenv("NOTION_TOKEN");
        if (!$token) {
            $this->markTestSkipped("Notion token is required to run integration tests.");
        }
        $client = Notion::create($token);

        $database = Database::create(DatabaseParent::page("60e79d42-4742-41ca-8d70-cc51660cbd3c"));

        $this->expectException(NotionException::class);
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
            ->withAdvancedTitle([ RichText::createText("Dummy database") ]);

        $database = $client->databases()->create($database);

        $client->databases()->delete($database);

        $this->expectException(NotionException::class);
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

        $this->expectException(NotionException::class);
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
        $this->assertCount(6, $pages);
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

        // 70s and 90s movies
        $query = Query::create()->withFilter(
            CompoundFilter::or(
                CompoundFilter::and(
                    DateFilter::property("Release date")->onOrAfter("1990-01-01"),
                    DateFilter::property("Release date")->onOrBefore("1999-12-31"),
                ),
                CompoundFilter::and(
                    DateFilter::property("Release date")->onOrAfter("1970-01-01"),
                    DateFilter::property("Release date")->onOrBefore("1979-12-31"),
                ),
            ),
        );

        $result = $client->databases()->query($database, $query);

        $this->assertCount(3, $result->pages());
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

        $this->expectException(NotionException::class);
        $client->databases()->query($database, $query);
    }
}
