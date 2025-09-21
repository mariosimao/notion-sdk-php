<?php

namespace Notion\Test\Integration;

use DateTimeImmutable;
use Notion\Common\Color;
use Notion\Common\Emoji;
use Notion\Common\RichText;
use Notion\Databases\Database;
use Notion\Databases\DatabaseParent;
use Notion\DataSources\Properties\Date;
use Notion\DataSources\Properties\RichTextProperty;
use Notion\DataSources\Properties\Select;
use Notion\DataSources\Properties\SelectOption;
use Notion\DataSources\Properties\Title;
use Notion\DataSources\Query;
use Notion\DataSources\Query\CompoundFilter;
use Notion\DataSources\Query\DateFilter;
use Notion\DataSources\Query\SelectFilter;
use Notion\DataSources\DataSource;
use Notion\DataSources\DataSourceParent;
use Notion\Exceptions\ApiException;
use Notion\Pages\Page;
use Notion\Pages\PageParent;
use Notion\Pages\Properties\Date as DateProp;
use Notion\Pages\Properties\Select as SelectProp;
use Notion\Search\Query as SearchQuery;
use PHPUnit\Framework\TestCase;

class DataSourcesTest extends TestCase
{
    private static int $bigDataSourceSize = 110;

    public function test_update_database(): void
    {
        $database = $this->newDatabase();
        $dataSource = $this->newDataSource($database->id);
        $client = Helper::client();

        $dataSource = $dataSource->addProperty(RichTextProperty::create("Test prop"));
        $dataSource = $client->dataSources()->update($dataSource);

        $this->assertEquals(
            "Test prop",
            $dataSource->properties()->get("Test prop")->metadata()->name
        );

        $client->databases()->delete($database);
    }

    public function test_query_all_pages_from_data_source(): void
    {
        $database = $this->newDatabase("Movies DB");
        $dataSource = self::moviesDataSource($database->id);

        $client = Helper::client();
        $pages = $client->dataSources()->queryAllPages($dataSource);

        $client->databases()->delete($database);

        $this->assertCount(5, $pages);
    }

    /** @group bigdb */
    public function test_query_big_database(): void
    {
        $client = Helper::client();
        $result = $client->search()->search(SearchQuery::title("Big dataset")->filterByDataSources());

        if (
            count($result->results) > 0 &&
            $result->results[0]::class === DataSource::class
        ) {
            /** @var DataSource */
            $dataSource = $result->results[0];
        } else {
            $database = $this->newDatabase("Big DB");
            $dataSource = self::bigDataSource($database->id);
        }

        $pages = $client->dataSources()->queryAllPages($dataSource);

        $this->assertCount(self::$bigDataSourceSize, $pages);
    }

    public function test_query_database(): void
    {
        $client = Helper::client();

        $database = $this->newDatabase("Movies DB");
        $dataSource = self::moviesDataSource($database->id);

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

        $result = $client->dataSources()->query($dataSource, $query);

        $client->databases()->delete($database);

        $this->assertCount(1, $result->pages);
    }

    public function test_query_inexistent_data_source(): void
    {
        $client = Helper::client();

        $dataSource = DataSource::create(DataSourceParent::database("a-database-id"));
        $query = Query::create();

        $this->expectException(ApiException::class);
        $client->dataSources()->query($dataSource, $query);
    }

    private static function moviesDataSource(string $databaseId): DataSource
    {
        $categories = [
            SelectOption::fromName("Action")->changeColor(Color::Orange),
            SelectOption::fromName("Comedy")->changeColor(Color::Yellow),
            SelectOption::fromName("Drama")->changeColor(Color::Red),
        ];

        $dataSource = self::newDataSource($databaseId, "Movies")
            ->changeProperties([
                "Movies" => Title::create("Movie"),
                "Release date" => Date::create("Release date"),
                "Category" => Select::create("Category", $categories),
            ])
            ->changeIcon(Emoji::fromString("🎬"));

        $dataSource = Helper::client()->dataSources()->create($dataSource);

        $pages = [
            self::moviePage($dataSource->id, "A Clockwork Orange", "1972-12-19", "Drama"),
            self::moviePage($dataSource->id, "Dead Poets Society", "1989-06-02", "Drama"),
            self::moviePage($dataSource->id, "Batman", "1989-10-26", "Action"),
            self::moviePage($dataSource->id, "The Mask", "1994-12-23", "Comedy"),
            self::moviePage($dataSource->id, "American Beauty", "1999-09-08", "Drama"),
        ];

        $client = Helper::client();
        foreach ($pages as $page) {
            $client->pages()->create($page);
        }

        return $dataSource;
    }

    private static function moviePage(
        string $dataSourceId,
        string $title,
        string $releaseDate,
        string $category
    ): Page {
        $date = new DateTimeImmutable($releaseDate);
        return Page::create(PageParent::dataSource($dataSourceId))
            ->changeTitle($title)
            ->addProperty("Release date", DateProp::create($date))
            ->addProperty("Category", SelectProp::fromName($category));
    }

    private static function bigDataSource(string $databaseId): DataSource
    {
        $client = Helper::client();

        $dataSource = DataSource::create(DataSourceParent::database($databaseId))
            ->changeTitle("Big dataset")
            ->changeIcon(Emoji::fromString("💾"))
            ->changeProperties([
                "Title" => Title::create("Title"),
            ]);

        $dataSource = $client->dataSources()->create($dataSource);

        $parent = PageParent::dataSource($dataSource->id);
        for ($i = 0; $i < self::$bigDataSourceSize; $i++) {
            $page = Page::create($parent)->changeTitle("Page #{$i}");
            $client->pages()->create($page);
        }

        return $dataSource;
    }

    private static function newDatabase(string|null $name = null): Database
    {
        $database = Database::create(DatabaseParent::page(Helper::testPageId()))
            ->changeTitle($name ?? "Test database");

        return Helper::client()->databases()->create($database);
    }

    private static function newDataSource(string $databaseId, string|null $name = null): DataSource
    {
        $dataSource = DataSource::create(DataSourceParent::database($databaseId))
            ->changeTitle($name ?? "Test data source")
            ->changeIcon(Emoji::fromString("🚀"));

        return Helper::client()->dataSources()->create($dataSource);
    }
}
