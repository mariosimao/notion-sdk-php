<?php

namespace Notion\Test\Unit\DataSources;

use ArrayObject;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\Response;
use Notion\Configuration;
use Notion\DataSources\DataSource;
use Notion\DataSources\Query;
use Notion\DataSources\Query\CompoundFilter;
use Notion\DataSources\Query\SelectFilter;
use Notion\DataSources\Query\Sort;
use Notion\DataSources\Query\TextFilter;
use Notion\Exceptions\QueryException;
use Notion\Notion;
use Notion\Pages\Page;
use Psr\Http\Message\RequestInterface;
use PHPUnit\Framework\TestCase;

/**
 * @psalm-import-type DataSourceJson from \Notion\DataSources\DataSource
 */
class ClientTest extends TestCase
{
    public function test_delete_sends_patch_with_in_trash_true(): void
    {
        $mock = new MockHandler([
            new Response(200, [], $this->dataSourceJson()),
        ]);
        $guzzle = new GuzzleClient([ "handler" => HandlerStack::create($mock) ]);
        $factory = new HttpFactory();
        $config = Configuration::createFromPsrImplementations("secret_123", $guzzle, $factory);
        $notion = Notion::createFromConfig($config);

        /** @var DataSourceJson $data */
        $data = json_decode($this->dataSourceJson(), true);
        $dataSource = DataSource::fromArray($data);

        $notion->dataSources()->delete($dataSource);

        $request = $mock->getLastRequest();
        $this->assertNotNull($request);
        $this->assertSame("PATCH", $request->getMethod());
        $this->assertSame("https://api.notion.com/v1/data_sources/{$dataSource->id}", (string) $request->getUri());

        /** @var array<string, mixed> $payload */
        $payload = json_decode((string) $request->getBody(), true);
        $this->assertSame(["in_trash" => true], $payload);
    }

    public function test_query_all_paginates_single_window(): void
    {
        $history = $this->history();
        $notion = $this->notion($history, [
            $this->queryResponse([ ["p1", "2024-01-01T10:00"], ["p2", "2024-01-01T10:01"] ], "cursor-1"),
            $this->queryResponse([ ["p3", "2024-01-01T10:02"] ]),
        ]);

        $pages = $notion->dataSources()->queryAll($this->dataSource(), Query::create()->changePageSize(2));

        $this->assertSame([ "p1", "p2", "p3" ], $this->ids($pages));
        $this->assertCount(2, $history);

        $first = $this->payload($history, 0);
        $this->assertSame(
            "https://api.notion.com/v1/data_sources/{$this->dataSource()->id}/query",
            (string) $this->request($history, 0)->getUri(),
        );
        $this->assertSame([ [ "timestamp" => "created_time", "direction" => "ascending" ] ], $first["sorts"]);
        $this->assertSame(2, $first["page_size"]);
        $this->assertArrayNotHasKey("filter", $first);
        $this->assertArrayNotHasKey("start_cursor", $first);

        $second = $this->payload($history, 1);
        $this->assertSame("cursor-1", $second["start_cursor"]);
    }

    public function test_query_all_windows_past_result_limit(): void
    {
        $history = $this->history();
        $notion = $this->notion($history, [
            $this->queryResponse([ ["p1", "2024-01-01T10:00"], ["p2", "2024-01-01T10:05"] ], "cursor-1"),
            $this->queryResponse([ ["p3", "2024-01-01T10:10"] ], null, true),
            $this->queryResponse([ ["p3", "2024-01-01T10:10"], ["p4", "2024-01-01T10:15"] ]),
        ]);

        $pages = $notion->dataSources()->queryAll($this->dataSource());

        $this->assertSame([ "p1", "p2", "p3", "p4" ], $this->ids($pages));
        $this->assertCount(3, $history);

        $third = $this->payload($history, 2);
        $this->assertArrayNotHasKey("start_cursor", $third);
        $this->assertSame([ [ "timestamp" => "created_time", "direction" => "ascending" ] ], $third["sorts"]);
        $this->assertSame(
            [
                "timestamp" => "created_time",
                "created_time" => [ "on_or_after" => "2024-01-01T10:10:00.000000Z" ],
            ],
            $third["filter"],
        );
    }

    public function test_query_all_combines_filter_with_window_bound(): void
    {
        $history = $this->history();
        $notion = $this->notion($history, [
            $this->queryResponse([ ["p1", "2024-01-01T10:00"] ], null, true),
            $this->queryResponse([ ["p1", "2024-01-01T10:00"], ["p2", "2024-01-01T10:05"] ]),
            $this->queryResponse([ ["p1", "2024-01-01T10:00"] ], null, true),
            $this->queryResponse([ ["p2", "2024-01-01T10:05"] ]),
        ]);

        $select = SelectFilter::property("Category")->equals("Drama");
        $text = TextFilter::property("Title")->contains("The");

        $notion->dataSources()->queryAll(
            $this->dataSource(),
            Query::create()->changeFilter($select),
        );
        $notion->dataSources()->queryAll(
            $this->dataSource(),
            Query::create()->changeFilter(CompoundFilter::and($select, $text)),
        );

        $bound = [
            "timestamp" => "created_time",
            "created_time" => [ "on_or_after" => "2024-01-01T10:00:00.000000Z" ],
        ];

        $this->assertSame($select->toArray(), $this->payload($history, 0)["filter"]);
        $this->assertSame(
            [ "and" => [ $select->toArray(), $bound ] ],
            $this->payload($history, 1)["filter"],
        );

        $this->assertSame(
            [ "and" => [ $select->toArray(), $text->toArray() ] ],
            $this->payload($history, 2)["filter"],
        );
        $this->assertSame(
            [ "and" => [ $select->toArray(), $text->toArray(), $bound ] ],
            $this->payload($history, 3)["filter"],
        );
    }

    public function test_query_all_rejects_top_level_or_filter_when_windowing(): void
    {
        $history = $this->history();
        $notion = $this->notion($history, [
            $this->queryResponse([ ["p1", "2024-01-01T10:00"] ], null, true),
        ]);

        $filter = CompoundFilter::or(
            SelectFilter::property("Category")->equals("Drama"),
            SelectFilter::property("Category")->equals("Action"),
        );

        $this->expectException(QueryException::class);
        $notion->dataSources()->queryAll($this->dataSource(), Query::create()->changeFilter($filter));
    }

    public function test_query_all_accepts_top_level_or_filter_under_limit(): void
    {
        $history = $this->history();
        $notion = $this->notion($history, [
            $this->queryResponse([ ["p1", "2024-01-01T10:00"] ]),
        ]);

        $filter = CompoundFilter::or(
            SelectFilter::property("Category")->equals("Drama"),
            SelectFilter::property("Category")->equals("Action"),
        );

        $pages = $notion->dataSources()->queryAll($this->dataSource(), Query::create()->changeFilter($filter));

        $this->assertSame([ "p1" ], $this->ids($pages));
    }

    public function test_query_all_throws_when_window_cannot_advance(): void
    {
        $history = $this->history();
        $notion = $this->notion($history, [
            $this->queryResponse([ ["p1", "2024-01-01T10:00"] ], null, true),
            $this->queryResponse([ ["p1", "2024-01-01T10:00"], ["p2", "2024-01-01T10:00"] ], null, true),
        ]);

        $this->expectException(QueryException::class);
        $notion->dataSources()->queryAll($this->dataSource());
    }

    public function test_query_all_throws_when_limit_reached_without_rows(): void
    {
        $history = $this->history();
        $notion = $this->notion($history, [
            $this->queryResponse([], null, true),
        ]);

        $this->expectException(QueryException::class);
        $notion->dataSources()->queryAll($this->dataSource());
    }

    public function test_query_all_preserves_custom_sorts_under_limit(): void
    {
        $history = $this->history();
        $notion = $this->notion($history, [
            $this->queryResponse([ ["p2", "2024-01-01T10:05"], ["p1", "2024-01-01T10:00"] ]),
        ]);

        $sort = Sort::property("Title")->descending();
        $pages = $notion->dataSources()->queryAll($this->dataSource(), Query::create()->addSort($sort));

        $this->assertSame([ "p2", "p1" ], $this->ids($pages));
        $this->assertCount(1, $history);
        $this->assertSame([ $sort->toArray() ], $this->payload($history, 0)["sorts"]);
    }

    public function test_query_all_with_custom_sorts_falls_back_to_windows(): void
    {
        $history = $this->history();
        $notion = $this->notion($history, [
            $this->queryResponse([ ["p3", "2024-01-01T10:10"], ["p1", "2024-01-01T10:00"] ], null, true),
            $this->queryResponse([
                ["p1", "2024-01-01T10:00"],
                ["p2", "2024-01-01T10:05"],
                ["p3", "2024-01-01T10:10"],
            ]),
        ]);

        $sort = Sort::property("Title")->descending();
        $pages = $notion->dataSources()->queryAll($this->dataSource(), Query::create()->addSort($sort));

        $this->assertSame([ "p3", "p1", "p2" ], $this->ids($pages));
        $this->assertCount(2, $history);
        $this->assertSame(
            [ [ "timestamp" => "created_time", "direction" => "ascending" ] ],
            $this->payload($history, 1)["sorts"],
        );
    }

    public function test_iterate_all_is_lazy(): void
    {
        $history = $this->history();
        $notion = $this->notion($history, [
            $this->queryResponse([ ["p1", "2024-01-01T10:00"] ], "cursor-1"),
            $this->queryResponse([ ["p2", "2024-01-01T10:05"] ]),
        ]);

        $iterator = $notion->dataSources()->iterateAll($this->dataSource());
        $this->assertCount(0, $history);

        $first = $iterator->current();
        $this->assertCount(1, $history);

        $iterator->next();
        $second = $iterator->current();

        $this->assertSame("p1", $first->id);
        $this->assertSame("p2", $second->id);
        $this->assertCount(2, $history);
    }

    /**
     * @param ArrayObject<int, array<array-key, mixed>> $history
     * @param list<Response> $responses
     */
    private function notion(ArrayObject $history, array $responses): Notion
    {
        $mock = new MockHandler($responses);
        $stack = HandlerStack::create($mock);
        $container = $history;
        $stack->push(Middleware::history($container));
        $guzzle = new GuzzleClient([ "handler" => $stack ]);
        $factory = new HttpFactory();
        $config = Configuration::createFromPsrImplementations("secret_123", $guzzle, $factory);

        return Notion::createFromConfig($config);
    }
    /** @param ArrayObject<int, array<array-key, mixed>> $history */
    private function request(ArrayObject $history, int $index): RequestInterface
    {
        /** @var RequestInterface $request */
        $request = $history[$index]["request"];

        return $request;
    }

    /** @return ArrayObject<int, array<array-key, mixed>> */
    private function history(): ArrayObject
    {
        /** @var ArrayObject<int, array<array-key, mixed>> */
        return new ArrayObject();
    }

    /**
     * @param ArrayObject<int, array<array-key, mixed>> $history
     *
     * @return array<string, mixed>
     */
    private function payload(ArrayObject $history, int $index): array
    {
        /** @var array<string, mixed> $payload */
        $payload = json_decode((string) $this->request($history, $index)->getBody(), true);

        return $payload;
    }

    /**
     * @param Page[] $pages
     *
     * @return string[]
     */
    private function ids(array $pages): array
    {
        return array_map(fn (Page $p) => $p->id, $pages);
    }

    private function dataSource(): DataSource
    {
        /** @var DataSourceJson $data */
        $data = json_decode($this->dataSourceJson(), true);

        return DataSource::fromArray($data);
    }

    /** @param list<array{0: string, 1: string}> $rows Page ID and created time */
    private function queryResponse(array $rows, string|null $nextCursor = null, bool $incomplete = false): Response
    {
        $results = array_map(fn (array $row) => [
            "object" => "page",
            "id" => $row[0],
            "created_time" => "{$row[1]}:00.000Z",
            "last_edited_time" => "{$row[1]}:00.000Z",
            "in_trash" => false,
            "icon" => null,
            "cover" => null,
            "properties" => [],
            "parent" => [
                "type" => "data_source_id",
                "data_source_id" => "a7e80c0b-a766-43c3-a9e9-21ce94595e0e",
            ],
            "url" => "https://notion.so/{$row[0]}",
        ], $rows);

        $body = [
            "object" => "list",
            "results" => $results,
            "has_more" => $nextCursor !== null,
            "next_cursor" => $nextCursor,
        ];

        if ($incomplete) {
            $body["request_status"] = [
                "type" => "incomplete",
                "incomplete_reason" => "query_result_limit_reached",
            ];
        }

        return new Response(200, [], json_encode($body));
    }

    private function dataSourceJson(): string
    {
        return '{
            "object": "data_source",
            "id": "a7e80c0b-a766-43c3-a9e9-21ce94595e0e",
            "created_time": "2020-12-08T12:00:00.000000Z",
            "last_edited_time": "2020-12-08T12:00:00.000000Z",
            "in_trash": false,
            "title": [],
            "description": [],
            "icon": null,
            "properties": {
                "Title": {
                    "id": "title",
                    "name": "Title",
                    "type": "title",
                    "title": {}
                }
            },
            "parent": {
                "type": "database_id",
                "database_id": "1ce62b6f-b7f3-4201-afd0-08acb02e61c6"
            },
            "url": "https://notion.so/a7e80c0ba76643c3a9e921ce94595e0e"
        }';
    }
}
