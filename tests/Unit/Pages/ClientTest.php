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
    private const PAGE_ID = "a7e80c0b-a766-43c3-a9e9-21ce94595e0e";

    public function test_create_without_filter_properties(): void
    {
        [$notion, $mock] = $this->notion();

        $notion->pages()->create(Page::create(PageParent::page(self::PAGE_ID)));

        $request = $mock->getLastRequest();
        $this->assertNotNull($request);
        $this->assertSame("POST", $request->getMethod());
        $this->assertSame("https://api.notion.com/v1/pages", (string) $request->getUri());
    }

    public function test_create_with_filter_properties(): void
    {
        [$notion, $mock] = $this->notion();

        $page = Page::create(PageParent::page(self::PAGE_ID))->changeTitle("Title");
        $notion->pages()->create($page, [], ["title", "Due date", "a%3Bb"]);

        $request = $mock->getLastRequest();
        $this->assertNotNull($request);
        $this->assertSame("POST", $request->getMethod());
        $this->assertSame(
            "https://api.notion.com/v1/pages"
                . "?filter_properties=title&filter_properties=Due%20date&filter_properties=a%253Bb",
            (string) $request->getUri(),
        );

        /** @var array{ properties: array<string, mixed> } $payload */
        $payload = json_decode((string) $request->getBody(), true);
        $this->assertArrayHasKey("title", $payload["properties"]);
    }

    public function test_update_without_filter_properties(): void
    {
        [$notion, $mock] = $this->notion();

        $notion->pages()->update($this->page());

        $request = $mock->getLastRequest();
        $this->assertNotNull($request);
        $this->assertSame("PATCH", $request->getMethod());
        $this->assertSame("https://api.notion.com/v1/pages/" . self::PAGE_ID, (string) $request->getUri());
    }

    public function test_update_with_filter_properties(): void
    {
        [$notion, $mock] = $this->notion();

        $notion->pages()->update($this->page(), ["title", "title", "Status"]);

        $request = $mock->getLastRequest();
        $this->assertNotNull($request);
        $this->assertSame("PATCH", $request->getMethod());
        $this->assertSame(
            "https://api.notion.com/v1/pages/" . self::PAGE_ID . "?filter_properties=title&filter_properties=Status",
            (string) $request->getUri(),
        );
    }

    /** @return array{0: Notion, 1: MockHandler} */
    private function notion(): array
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode($this->pageArray())),
        ]);
        $guzzle = new GuzzleClient([ "handler" => HandlerStack::create($mock) ]);
        $factory = new HttpFactory();
        $config = Configuration::createFromPsrImplementations("secret_123", $guzzle, $factory);

        return [Notion::createFromConfig($config), $mock];
    }

    private function page(): Page
    {
        return Page::fromArray($this->pageArray());
    }

    /** @return PageJson */
    private function pageArray(): array
    {
        return [
            "object" => "page",
            "id" => self::PAGE_ID,
            "created_time" => "2020-12-08T12:00:00.000000Z",
            "last_edited_time" => "2020-12-08T12:00:00.000000Z",
            "in_trash" => false,
            "icon" => null,
            "cover" => null,
            "properties" => [],
            "parent" => [
                "type" => "page_id",
                "page_id" => "1ce62b6f-b7f3-4201-afd0-08acb02e61c6",
            ],
            "url" => "https://notion.so/a7e80c0ba76643c3a9e921ce94595e0e",
        ];
    }
}
