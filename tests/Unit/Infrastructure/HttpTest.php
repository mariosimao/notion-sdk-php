<?php

namespace Notion\Test\Unit\Infrastructure;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\Response;
use Notion\Configuration;
use Notion\Exceptions\ConflictException;
use Notion\Exceptions\RateLimitedException;
use Notion\Infrastructure\Http;
use Notion\Notion;
use Notion\Pages\Page;
use Notion\Pages\PageParent;
use PHPUnit\Framework\TestCase;

final class HttpTest extends TestCase
{
    public function test_create_request(): void
    {
        $client = new Client();
        $factory = new HttpFactory();
        $config = Configuration::createFromPsrImplementations("secret_123", $client, $factory);

        $request = Http::createRequest("https://api.notion.com/v1/users", $config);

        $this->assertSame("GET", $request->getMethod());
        $this->assertSame("https://api.notion.com/v1/users", (string) $request->getUri());
        $this->assertSame("Bearer secret_123", $request->getHeaderLine("Authorization"));
        $this->assertSame($config->version, $request->getHeaderLine("Notion-Version"));
    }

    public function test_create_auth_request(): void
    {
        $client = new Client();
        $factory = new HttpFactory();
        $config = Configuration::createFromPsrImplementations("secret_123", $client, $factory);

        $request = Http::createAuthRequest(
            "https://api.notion.com/v1/oauth/token",
            $config,
            "client_id_123",
            "client_secret_456",
        );

        $this->assertSame("GET", $request->getMethod());
        $this->assertSame("https://api.notion.com/v1/oauth/token", (string) $request->getUri());
        $expectedAuth = "Basic " . base64_encode("client_id_123:client_secret_456");
        $this->assertSame($expectedAuth, $request->getHeaderLine("Authorization"));
        $this->assertSame($config->version, $request->getHeaderLine("Notion-Version"));
    }

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

    public function test_retry_sending_request_after_rate_limit_errors(): void
    {
        // Body value (0s) is used instead of the header value (30s)
        $mock = new MockHandler([
            new Response(429, [ "Retry-After" => "30" ], $this->rateLimitedErrorJson("0")),
            new Response(429, [ "Retry-After" => "30" ], $this->rateLimitedErrorJson("0")),
            new Response(201, [], $this->createdPageJson()),
        ]);
        $notion = $this->notionWithMock($mock, fn (Configuration $c) => $c->enableRetryOnRateLimit(2));

        $start = microtime(true);
        $notion->pages()->create(Page::create(PageParent::workspace()));

        $this->assertCount(0, $mock);
        $this->assertLessThan(5, microtime(true) - $start);
    }

    public function test_retry_after_rate_limit_uses_header_without_body_value(): void
    {
        $mock = new MockHandler([
            new Response(429, [ "Retry-After" => "0" ], $this->rateLimitedErrorJson(null)),
            new Response(201, [], $this->createdPageJson()),
        ]);
        $notion = $this->notionWithMock($mock, fn (Configuration $c) => $c->enableRetryOnRateLimit(1));

        $notion->pages()->create(Page::create(PageParent::workspace()));

        $this->assertCount(0, $mock);
    }

    public function test_retry_after_rate_limit_respects_max_wait(): void
    {
        $mock = new MockHandler([
            new Response(429, [ "Retry-After" => "60" ], $this->rateLimitedErrorJson("60")),
            new Response(201, [], $this->createdPageJson()),
        ]);
        $notion = $this->notionWithMock($mock, fn (Configuration $c) => $c->enableRetryOnRateLimit(1, 0));

        $start = microtime(true);
        $notion->pages()->create(Page::create(PageParent::workspace()));

        $this->assertCount(0, $mock);
        $this->assertLessThan(5, microtime(true) - $start);
    }

    public function test_retry_sending_request_after_many_rate_limit_errors(): void
    {
        $mock = new MockHandler([
            new Response(429, [], $this->rateLimitedErrorJson("0")),
            new Response(429, [], $this->rateLimitedErrorJson("0")),
            new Response(429, [], $this->rateLimitedErrorJson("0")),
        ]);
        $notion = $this->notionWithMock($mock, fn (Configuration $c) => $c->enableRetryOnRateLimit(2));

        $this->expectException(RateLimitedException::class);
        $notion->pages()->create(Page::create(PageParent::workspace()));
    }

    public function test_do_not_retry_blocked_requests(): void
    {
        $mock = new MockHandler([
            new Response(429, [], $this->rateLimitedErrorJson("0", "public_api_request_blocked")),
            new Response(201, [], $this->createdPageJson()),
        ]);
        $notion = $this->notionWithMock($mock, fn (Configuration $c) => $c->enableRetryOnRateLimit(2));

        try {
            $notion->pages()->create(Page::create(PageParent::workspace()));
            $this->fail("Expected RateLimitedException");
        } catch (RateLimitedException $e) {
            $this->assertFalse($e->isRetryable());
        }
        $this->assertCount(1, $mock);
    }

    public function test_do_not_retry_rate_limit_when_disabled(): void
    {
        $mock = new MockHandler([
            new Response(429, [], $this->rateLimitedErrorJson("0")),
            new Response(201, [], $this->createdPageJson()),
        ]);
        $notion = $this->notionWithMock($mock, fn (Configuration $c) => $c->disableRetryOnRateLimit());

        try {
            $notion->pages()->create(Page::create(PageParent::workspace()));
            $this->fail("Expected RateLimitedException");
        } catch (RateLimitedException $e) {
            $this->assertSame(0, $e->retryAfter());
        }
        $this->assertCount(1, $mock);
    }

    /** @param callable(Configuration): Configuration $configure */
    private function notionWithMock(MockHandler $mock, callable $configure): Notion
    {
        $client = new Client([ "handler" => HandlerStack::create($mock)]);
        $config = Configuration::createFromPsrImplementations("secret_123", $client, new HttpFactory());

        return Notion::createFromConfig($configure($config));
    }

    private function rateLimitedErrorJson(?string $retryAfter, string $reason = "public_api_request_rate_limit"): string
    {
        $additionalData = [ "rate_limit_reason" => $reason ];
        if ($retryAfter !== null) {
            $additionalData["retry_after"] = $retryAfter;
        }

        return json_encode([
            "object" => "error",
            "status" => 429,
            "code" => "rate_limited",
            "message" => "You have been rate limited. Please try again later.",
            "additional_data" => $additionalData,
        ], JSON_THROW_ON_ERROR);
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
            "in_trash": false,
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
