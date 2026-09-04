<?php

namespace Notion\Test\Unit\Pages;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\Response;
use Notion\Configuration;
use Notion\Notion;
use Notion\Pages\Page;
use Notion\Pages\PageParent;
use PHPUnit\Framework\TestCase;

/**
 * @psalm-import-type PageJson from \Notion\Pages\Page
 */
class ClientTest extends TestCase
{
    public function test_create_page_with_data_source_parent(): void
    {
        $mock = new MockHandler([
            new Response(200, [], $this->pageJson()),
        ]);
        $guzzle = new GuzzleClient([ "handler" => HandlerStack::create($mock) ]);
        $factory = new HttpFactory();
        $config = Configuration::createFromPsrImplementations("secret_123", $guzzle, $factory);
        $notion = Notion::createFromConfig($config);

        $parent = PageParent::dataSource(
            "0181c3aa-1112-489f-b34a-515b4e3583ed",
            "7a774b5d-ca74-4679-9f18-689b5a98f138",
        );
        $page = Page::create($parent);

        $notion->pages()->create($page);

        $request = $mock->getLastRequest();
        $this->assertNotNull($request);
        $this->assertSame("POST", $request->getMethod());
        $this->assertSame("https://api.notion.com/v1/pages", (string) $request->getUri());

        /** @var array{parent: array<string, mixed>} $payload */
        $payload = json_decode((string) $request->getBody(), true);
        $this->assertSame([
            "type" => "data_source_id",
            "data_source_id" => "0181c3aa-1112-489f-b34a-515b4e3583ed",
        ], $payload["parent"]);
        $this->assertArrayNotHasKey("database_id", $payload["parent"]);
    }

    public function test_update_page_with_data_source_parent(): void
    {
        $mock = new MockHandler([
            new Response(200, [], $this->pageJson()),
        ]);
        $guzzle = new GuzzleClient([ "handler" => HandlerStack::create($mock) ]);
        $factory = new HttpFactory();
        $config = Configuration::createFromPsrImplementations("secret_123", $guzzle, $factory);
        $notion = Notion::createFromConfig($config);

        /** @var PageJson $pageData */
        $pageData = json_decode($this->pageJson(), true);
        $page = Page::fromArray($pageData);

        $notion->pages()->update($page);

        $request = $mock->getLastRequest();
        $this->assertNotNull($request);
        $this->assertSame("PATCH", $request->getMethod());
        $this->assertSame("https://api.notion.com/v1/pages/{$page->id}", (string) $request->getUri());

        /** @var array{parent: array<string, mixed>} $payload */
        $payload = json_decode((string) $request->getBody(), true);
        $this->assertSame([
            "type" => "data_source_id",
            "data_source_id" => "0181c3aa-1112-489f-b34a-515b4e3583ed",
        ], $payload["parent"]);
        $this->assertArrayNotHasKey("database_id", $payload["parent"]);
    }

    private function pageJson(): string
    {
        return '{
            "object": "page",
            "id": "a7e80c0b-a766-43c3-a9e9-21ce94595e0e",
            "created_time": "2020-12-08T12:00:00.000000Z",
            "last_edited_time": "2020-12-08T12:00:00.000000Z",
            "in_trash": false,
            "icon": null,
            "cover": null,
            "properties": {},
            "parent": {
                "type": "data_source_id",
                "data_source_id": "0181c3aa-1112-489f-b34a-515b4e3583ed",
                "database_id": "7a774b5d-ca74-4679-9f18-689b5a98f138"
            },
            "url": "https://notion.so/a7e80c0ba76643c3a9e921ce94595e0e"
        }';
    }
}
