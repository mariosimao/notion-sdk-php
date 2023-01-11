<?php

namespace Notion\Test\Unit\Infrastructure;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\Response;
use Notion\Configuration;
use Notion\Exceptions\ConflictException;
use Notion\Notion;
use Notion\Pages\Page;
use Notion\Pages\PageParent;
use PHPUnit\Framework\TestCase;

final class HttpTest extends TestCase
{
    public function test_retry_sending_request_after_conflict_errors(): void
    {
        $mock = new MockHandler([
            new Response(500, [], $this->conflictErrorJson()),
            new Response(500, [], $this->conflictErrorJson()),
            new Response(201, [], $this->createdPageJson()),
        ]);
        $client = new Client([ "handler" => HandlerStack::create($mock)]);

        $factory = new HttpFactory();
        $config = Configuration::createFromPsrImplementations("secret_123", $client, $factory)
                    ->enableRetryOnConflict(2);

        $notion = Notion::createFromConfig($config);
        $page = Page::create(PageParent::workspace());

        $notion->pages()->create($page);

        $this->assertCount(0, $mock);
    }

    public function test_retry_sending_request_after_many_conflict_errors(): void
    {
        $mock = new MockHandler([
            new Response(500, [], $this->conflictErrorJson()),
            new Response(500, [], $this->conflictErrorJson()),
            new Response(500, [], $this->conflictErrorJson()),
        ]);
        $client = new Client([ "handler" => HandlerStack::create($mock)]);

        $factory = new HttpFactory();
        $config = Configuration::createFromPsrImplementations("secret_123", $client, $factory)
                    ->enableRetryOnConflict(2);

        $notion = Notion::createFromConfig($config);
        $page = Page::create(PageParent::workspace());

        $this->expectException(ConflictException::class);
        $notion->pages()->create($page);
    }

    private function conflictErrorJson(): string
    {
        return '{
            "object": "error",
            "status": 500,
            "code": "conflict_error",
            "message": "Conflict occurred while saving. Please try again."
        }';
    }

    private function createdPageJson(): string
    {
        return '{
            "object": "page",
            "id": "ff747ce6-bb89-4c54-80c3-a248a2c78bd9",
            "created_time": "2023-01-11T21:04:00.000Z",
            "last_edited_time": "2023-01-11T21:04:00.000Z",
            "created_by": {
                "object": "user",
                "id": "e8f2d77a-8756-43f6-bc87-0dc2bf9115fa"
            },
            "last_edited_by": {
                "object": "user",
                "id": "e8f2d77a-8756-43f6-bc87-0dc2bf9115fa"
            },
            "cover": null,
            "icon": null,
            "parent": {
                "type": "page_id",
                "page_id": "cf735738-35e3-44aa-b3d4-aca944c8f421"
            },
            "archived": false,
            "properties": {
                "title": {
                    "id": "title",
                    "type": "title",
                    "title": []
                }
            },
            "url": "https://www.notion.so/ff747ce6bb894c5480c3a248a2c78bd9"
        }';
    }
}
