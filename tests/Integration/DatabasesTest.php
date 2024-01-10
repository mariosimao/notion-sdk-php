<?php

namespace Notion\Test\Integration;

use DateTimeImmutable;
use Notion\Common\Color;
use Notion\Common\Emoji;
use Notion\Common\RichText;
use Notion\Databases\Database;
use Notion\Databases\DatabaseParent;
use Notion\Databases\Properties\Date;
use Notion\Databases\Properties\RichTextProperty;
use Notion\Databases\Properties\Select;
use Notion\Databases\Properties\SelectOption;
use Notion\Databases\Properties\Title;
use Notion\Databases\Query;
use Notion\Databases\Query\CompoundFilter;
use Notion\Databases\Query\DateFilter;
use Notion\Databases\Query\SelectFilter;
use Notion\Exceptions\ApiException;
use Notion\Pages\Page;
use Notion\Pages\PageParent;
use Notion\Pages\Properties\Date as DateProp;
use Notion\Pages\Properties\Select as SelectProp;
use Notion\Search\Query as SearchQuery;
use PHPUnit\Framework\TestCase;

class DatabasesTest extends TestCase
{
    private static int $bigDatabaseSize = 110;

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

    public function test_update_database(): void
    {
        $client = Helper::client();

        $database = Database::create(DatabaseParent::page(Helper::testPageId()))
            ->changeTitle("Test database");
        $database = $client->databases()->create($database);

        $database = $database->addProperty(RichTextProperty::create("Test prop"));
        $database = $client->databases()->update($database);

        $this->assertEquals(
            "Test prop",
            $database->properties()->get("Test prop")->metadata()->name
        );

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

    public function test_query_all_pages_from_database(): void
    {
        $database = self::moviesDatabase();

        $client = Helper::client();
        $pages = $client->databases()->queryAllPages($database);

        $client->databases()->delete($database);

        $this->assertCount(5, $pages);
    }

    /** @group bigdb */
    public function test_query_big_database(): void
    {
        $client = Helper::client();
        $result = $client->search()->search(SearchQuery::title("Big dataset")->filterByDatabases());

        if (
            count($result->results) > 0 &&
            $result->results[0]::class === Database::class
        ) {
            /** @var Database */
            $bigDatabase = $result->results[0];
        } else {
            $bigDatabase = self::bigDatabase();
        }

        $pages = $client->databases()->queryAllPages($bigDatabase);

        $this->assertCount(self::$bigDatabaseSize, $pages);
    }

    public function test_query_database(): void
    {
        $client = Helper::client();

        $database = self::moviesDatabase();

        /**
         * 90s drama movies
         *
         * Category == Drama AND
         * Release >= 1990-01-01 AND
         * Release <= 1999-12-31
         *
         */
        $query = Query::create()->changeFilter(
            CompoundFilter::and(
                SelectFilter::property("Category")->equals("Drama"),
                DateFilter::property("Release date")->onOrAfter("1990-01-01"),
                DateFilter::property("Release date")->onOrBefore("1999-12-31"),
            ),
        );

        $result = $client->databases()->query($database, $query);

        $client->databases()->delete($database);

        $this->assertCount(1, $result->pages);
    }

    public function test_query_inexistent_database(): void
    {
        $client = Helper::client();

        $database = Database::create(DatabaseParent::page("a-page-id"));
        $query = Query::create();

        $this->expectException(ApiException::class);
        $client->databases()->query($database, $query);
    }

    private static function moviesDatabase(): Database
    {
        $databaseParent = DatabaseParent::page(Helper::testPageId());

        $categories = [
            SelectOption::fromName("Action")->changeColor(Color::Orange),
            SelectOption::fromname("Comedy")->changeColor(Color::Yellow),
            SelectOption::fromName("Drama")->changeColor(Color::Red),
        ];

        $database = Database::create($databaseParent)
            ->changeTitle("Movies")
            ->changeProperties([
                "Movies" => Title::create("Movie"),
                "Release date" => Date::create("Release date"),
                "Category" => Select::create("Category", $categories),
            ]);

        $database = Helper::client()->databases()->create($database);

        $pages = [
            self::moviePage($database->id, "A Clockwork Orange", "1972-12-19", "Drama"),
            self::moviePage($database->id, "Dead Poets Society", "1989-06-02", "Drama"),
            self::moviePage($database->id, "Batman", "1989-10-26", "Action"),
            self::moviePage($database->id, "The Mask", "1994-12-23", "Comedy"),
            self::moviePage($database->id, "American Beauty", "1999-09-08", "Drama"),
        ];

        $client = Helper::client();
        foreach ($pages as $page) {
            $client->pages()->create($page);
        }

        return $database;
    }

    private static function moviePage(
        string $databaseId,
        string $title,
        string $releaseDate,
        string $category
    ): Page {
        $date = new DateTimeImmutable($releaseDate);
        return Page::create(PageParent::database($databaseId))
            ->changeTitle($title)
            ->addProperty("Release date", DateProp::create($date))
            ->addProperty("Category", SelectProp::fromname($category));
    }

    private static function bigDatabase(): Database
    {
        $client = Helper::client();

        $database = Database::create(DatabaseParent::page(Helper::testPageId()))
            ->changeTitle("Big dataset");

        $database = $client->databases()->create($database);

        $parent = PageParent::database($database->id);
        for ($i = 0; $i < self::$bigDatabaseSize; $i++) {
            $page = Page::create($parent)->changeTitle("Page #{$i}");
            $client->pages()->create($page);
        }

        return $database;
    }
}
