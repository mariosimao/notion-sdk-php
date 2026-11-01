<?php

namespace Notion\Test\Unit\Views;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\Response;
use Notion\Configuration;
use Notion\DataSources\Query\SelectFilter;
use Notion\DataSources\Query\Sort;
use Notion\Notion;
use Notion\Views\Configuration\BoardConfiguration;
use Notion\Views\Configuration\GroupBy;
use Notion\Views\ListQuery;
use Notion\Views\QuickFilter;
use Notion\Views\View;
use Notion\Views\ViewPosition;
use Notion\Views\ViewTarget;
use Notion\Views\ViewType;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

class ClientTest extends TestCase
{
    /** @var array<int, array{request: RequestInterface}> */
    private array $history = [];

    public function test_list(): void
    {
        $notion = $this->notion([
            $this->json([
                "object"      => "list",
                "results"     => [[ "object" => "view", "id" => "v1" ], [ "object" => "view", "id" => "v2" ]],
                "next_cursor" => "cursor",
                "has_more"    => true,
                "type"        => "view",
                "view"        => [],
            ]),
        ]);

        $list = $notion->views()->list(ListQuery::database("db")->changePageSize(2));

        $this->assertSame([ "v1", "v2" ], $list->viewIds);
        $this->assertSame("cursor", $list->nextCursor);
        $this->assertTrue($list->hasMore);
        $this->assertSame("GET", $this->request(0)->getMethod());
        $this->assertSame(
            "https://api.notion.com/v1/views?database_id=db&page_size=2",
            (string) $this->request(0)->getUri(),
        );
    }

    public function test_list_all(): void
    {
        $notion = $this->notion([
            $this->json([
                "results"     => [[ "object" => "view", "id" => "v1" ]],
                "next_cursor" => "c",
                "has_more"    => true,
            ]),
            $this->json([
                "results"     => [[ "object" => "view", "id" => "v2" ]],
                "next_cursor" => null,
                "has_more"    => false,
            ]),
        ]);

        $viewIds = $notion->views()->listAll(ListQuery::dataSource("ds"));

        $this->assertSame([ "v1", "v2" ], $viewIds);
        $this->assertSame(
            "https://api.notion.com/v1/views?data_source_id=ds&page_size=100",
            (string) $this->request(0)->getUri(),
        );
        $this->assertSame(
            "https://api.notion.com/v1/views?data_source_id=ds&start_cursor=c&page_size=100",
            (string) $this->request(1)->getUri(),
        );
    }

    public function test_find(): void
    {
        $notion = $this->notion([ $this->json(ViewTest::viewArray()) ]);

        $view = $notion->views()->find("view-id");

        $this->assertSame("view-id", $view->id);
        $this->assertSame("GET", $this->request(0)->getMethod());
        $this->assertSame("https://api.notion.com/v1/views/view-id", (string) $this->request(0)->getUri());
    }

    public function test_create(): void
    {
        $notion = $this->notion([ $this->json(ViewTest::viewArray()) ]);

        $view = View::create("ds-id", "Board", ViewType::Board)
            ->changeFilter(SelectFilter::property("Status")->equals("Done"))
            ->addSort(Sort::property("Name"))
            ->addQuickFilter(QuickFilter::withoutCriteria("Tags"))
            ->changeConfiguration(BoardConfiguration::create(GroupBy::select("abc")));

        $notion->views()->create($view, ViewTarget::database("db-id", ViewPosition::start()));

        $request = $this->request(0);
        $this->assertSame("POST", $request->getMethod());
        $this->assertSame("https://api.notion.com/v1/views", (string) $request->getUri());

        $payload = $this->payload($request);
        $this->assertSame("ds-id", $payload["data_source_id"]);
        $this->assertSame("Board", $payload["name"]);
        $this->assertSame("board", $payload["type"]);
        $this->assertSame([ "property" => "Status", "select" => [ "equals" => "Done" ] ], $payload["filter"]);
        $this->assertSame([[ "property" => "Name", "direction" => "ascending" ]], $payload["sorts"]);
        $this->assertSame([ "Tags" => [] ], $payload["quick_filters"]);
        $this->assertStringContainsString('"quick_filters":{"Tags":{}}', (string) $request->getBody());
        $this->assertIsArray($payload["configuration"]);
        $this->assertSame("board", $payload["configuration"]["type"]);
        $this->assertSame("db-id", $payload["database_id"]);
        $this->assertSame([ "type" => "start" ], $payload["position"]);
    }

    public function test_create_minimal(): void
    {
        $notion = $this->notion([ $this->json(ViewTest::viewArray()) ]);

        $notion->views()->create(View::create("ds-id", "Table", ViewType::Table), ViewTarget::database("db-id"));

        $this->assertSame([
            "data_source_id" => "ds-id",
            "name"           => "Table",
            "type"           => "table",
            "database_id"    => "db-id",
        ], $this->payload($this->request(0)));
    }

    public function test_update(): void
    {
        $notion = $this->notion([ $this->json(ViewTest::viewArray()) ]);

        $view = View::fromArray(ViewTest::viewArray())
            ->changeName("Renamed")
            ->removeFilter()
            ->changeSorts()
            ->removeQuickFilter("abc");

        $notion->views()->update($view);

        $request = $this->request(0);
        $this->assertSame("PATCH", $request->getMethod());
        $this->assertSame("https://api.notion.com/v1/views/view-id", (string) $request->getUri());

        $payload = $this->payload($request);
        $this->assertSame("Renamed", $payload["name"]);
        $this->assertNull($payload["filter"]);
        $this->assertNull($payload["sorts"]);
        $this->assertSame([ "def" => [], "abc" => null ], $payload["quick_filters"]);
        $this->assertIsArray($payload["configuration"]);
        $this->assertSame("table", $payload["configuration"]["type"]);
        /** @var array{properties: list<array>} $configuration */
        $configuration = $payload["configuration"];
        $this->assertArrayNotHasKey("property_name", $configuration["properties"][0]);
    }

    public function test_update_without_quick_filters(): void
    {
        $array = ViewTest::viewArray();
        unset($array["quick_filters"], $array["configuration"]);
        $notion = $this->notion([ $this->json(ViewTest::viewArray()) ]);

        $notion->views()->update(View::fromArray($array));

        $payload = $this->payload($this->request(0));
        $this->assertArrayNotHasKey("quick_filters", $payload);
        $this->assertArrayNotHasKey("configuration", $payload);
    }

    public function test_delete(): void
    {
        $notion = $this->notion([
            $this->json([ "object" => "view", "id" => "view-id", "type" => "table" ]),
            $this->json([ "object" => "view", "id" => "view-id", "type" => "table" ]),
        ]);

        $notion->views()->delete("view-id");
        $notion->views()->delete(View::fromArray(ViewTest::viewArray()));

        $this->assertSame("DELETE", $this->request(0)->getMethod());
        $this->assertSame("https://api.notion.com/v1/views/view-id", (string) $this->request(0)->getUri());
        $this->assertSame("https://api.notion.com/v1/views/view-id", (string) $this->request(1)->getUri());
    }

    public function test_query_and_results(): void
    {
        $notion = $this->notion([
            $this->json($this->viewQueryArray()),
            $this->json([
                "object"         => "list",
                "results"        => [[ "object" => "page", "id" => "p3" ]],
                "next_cursor"    => null,
                "has_more"       => false,
                "request_status" => [ "type" => "incomplete", "incomplete_reason" => "query_result_limit_reached" ],
            ]),
            $this->json([ "object" => "view_query", "id" => "q1", "deleted" => true ]),
        ]);

        $query = $notion->views()->query("view-id", 2);

        $this->assertSame("q1", $query->id);
        $this->assertSame("view-id", $query->viewId);
        $this->assertSame(3, $query->totalCount);
        $this->assertSame([ "p1", "p2" ], $query->pageIds);
        $this->assertSame("cursor", $query->nextCursor);
        $this->assertTrue($query->hasMore);
        $this->assertFalse($query->isIncomplete);
        $this->assertSame("2025-01-01", $query->expiresAt->format("Y-m-d"));
        $this->assertSame("POST", $this->request(0)->getMethod());
        $this->assertSame("https://api.notion.com/v1/views/view-id/queries", (string) $this->request(0)->getUri());
        $this->assertSame([ "page_size" => 2 ], $this->payload($this->request(0)));

        $result = $notion->views()->queryResults($query, "cursor", 2);

        $this->assertSame([ "p3" ], $result->pageIds);
        $this->assertFalse($result->hasMore);
        $this->assertTrue($result->isIncomplete);
        $this->assertSame("GET", $this->request(1)->getMethod());
        $this->assertSame(
            "https://api.notion.com/v1/views/view-id/queries/q1?start_cursor=cursor&page_size=2",
            (string) $this->request(1)->getUri(),
        );

        $notion->views()->deleteQuery($query);

        $this->assertSame("DELETE", $this->request(2)->getMethod());
        $this->assertSame("https://api.notion.com/v1/views/view-id/queries/q1", (string) $this->request(2)->getUri());
    }

    public function test_query_without_page_size_sends_empty_object(): void
    {
        $notion = $this->notion([ $this->json($this->viewQueryArray()) ]);

        $notion->views()->query(View::fromArray(ViewTest::viewArray()));

        $this->assertSame("{}", (string) $this->request(0)->getBody());
    }

    public function test_query_all_pages(): void
    {
        $notion = $this->notion([
            $this->json($this->viewQueryArray()),
            $this->json([
                "results"     => [[ "object" => "page", "id" => "p3" ]],
                "next_cursor" => null,
                "has_more"    => false,
            ]),
            $this->json([ "object" => "view_query", "id" => "q1", "deleted" => true ]),
        ]);

        $pageIds = $notion->views()->queryAllPages("view-id");

        $this->assertSame([ "p1", "p2", "p3" ], $pageIds);
        $this->assertSame([ "page_size" => 100 ], $this->payload($this->request(0)));
        $this->assertSame(
            "https://api.notion.com/v1/views/view-id/queries/q1?start_cursor=cursor&page_size=100",
            (string) $this->request(1)->getUri(),
        );
        $this->assertSame("DELETE", $this->request(2)->getMethod());
    }

    private function viewQueryArray(): array
    {
        return [
            "object"      => "view_query",
            "id"          => "q1",
            "view_id"     => "view-id",
            "expires_at"  => "2025-01-01T00:15:00.000Z",
            "total_count" => 3,
            "results"     => [[ "object" => "page", "id" => "p1" ], [ "object" => "page", "id" => "p2" ]],
            "next_cursor" => "cursor",
            "has_more"    => true,
        ];
    }

    /** @param list<Response> $responses */
    private function notion(array $responses): Notion
    {
        $this->history = [];
        $stack = HandlerStack::create(new MockHandler($responses));
        /** @psalm-suppress MixedPropertyTypeCoercion */
        $stack->push(\GuzzleHttp\Middleware::history($this->history));
        $guzzle = new GuzzleClient([ "handler" => $stack ]);
        $config = Configuration::createFromPsrImplementations("secret_123", $guzzle, new HttpFactory());

        return Notion::createFromConfig($config);
    }

    private function json(array $body): Response
    {
        return new Response(200, [], json_encode($body));
    }

    private function request(int $index): RequestInterface
    {
        return $this->history[$index]["request"];
    }

    private function payload(RequestInterface $request): array
    {
        /** @var array $payload */
        $payload = json_decode((string) $request->getBody(), true);

        return $payload;
    }
}
