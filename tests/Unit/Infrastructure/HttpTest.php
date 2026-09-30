<?php

namespace Notion\Test\Unit\Infrastructure;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\Response;
use Notion\Configuration;
use Notion\Exceptions\ApiException;
use Notion\Exceptions\ConflictException;
use Notion\Infrastructure\Http;
use Notion\Notion;
use Notion\Pages\Page;
use Notion\Pages\PageParent;
use Notion\RetryPolicy;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

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
            new Response(409, [], $this->conflictErrorJson()),
            new Response(409, [], $this->conflictErrorJson()),
            new Response(201, [], $this->createdPageJson()),
        ]);

        $this->createPage($mock);

        $this->assertCount(0, $mock);
    }

    public function test_retry_sending_request_after_many_conflict_errors(): void
    {
        $mock = new MockHandler([
            new Response(409, [], $this->conflictErrorJson()),
            new Response(409, [], $this->conflictErrorJson()),
            new Response(409, [], $this->conflictErrorJson()),
        ]);

        $this->expectException(ConflictException::class);
        $this->createPage($mock);
    }

    public function test_retry_rate_limited_write_requests(): void
    {
        $mock = new MockHandler([
            new Response(429, ["Retry-After" => "0"], $this->errorJson(429, "rate_limited")),
            new Response(201, [], $this->createdPageJson()),
        ]);

        $this->createPage($mock);

        $this->assertCount(0, $mock);
    }

    public function test_retry_overloaded_write_requests(): void
    {
        $mock = new MockHandler([
            new Response(529, ["Retry-After" => "0"], $this->errorJson(529, "service_overload")),
            new Response(201, [], $this->createdPageJson()),
        ]);

        $this->createPage($mock);

        $this->assertCount(0, $mock);
    }

    public function test_resend_request_body_on_retry(): void
    {
        $history = [];
        $mock = new MockHandler([
            new Response(429, ["Retry-After" => "0"], $this->errorJson(429, "rate_limited")),
            new Response(201, [], $this->createdPageJson()),
        ]);
        $stack = HandlerStack::create($mock);
        $stack->push(Middleware::history($history));

        $this->notion($stack)->pages()->create(Page::create(PageParent::workspace()));

        /** @var list<array{request: RequestInterface}> $history */
        $this->assertCount(2, $history);
        $first = (string) $history[0]["request"]->getBody();
        $second = (string) $history[1]["request"]->getBody();
        $this->assertNotSame("", $first);
        $this->assertSame($first, $second);
    }

    public function test_do_not_retry_blocked_requests(): void
    {
        $mock = new MockHandler([
            new Response(429, [], $this->errorJson(429, "rate_limited", "public_api_request_blocked")),
            new Response(201, [], $this->createdPageJson()),
        ]);

        try {
            $this->createPage($mock);
            $this->fail("Expected ApiException");
        } catch (ApiException $e) {
            $this->assertSame("rate_limited", $e->notionCode);
        }

        $this->assertCount(1, $mock);
    }

    public function test_retry_server_errors_on_read_requests(): void
    {
        $mock = new MockHandler([
            new Response(500, [], $this->errorJson(500, "internal_server_error")),
            new Response(503, [], $this->errorJson(503, "service_unavailable")),
            new Response(200, [], $this->createdPageJson()),
        ]);

        $this->notion(HandlerStack::create($mock))->pages()->find("ff747ce6-bb89-4c54-80c3-a248a2c78bd9");

        $this->assertCount(0, $mock);
    }

    public function test_do_not_retry_server_errors_on_write_requests(): void
    {
        $mock = new MockHandler([
            new Response(503, [], $this->errorJson(503, "service_unavailable")),
            new Response(201, [], $this->createdPageJson()),
        ]);

        try {
            $this->createPage($mock);
            $this->fail("Expected ApiException");
        } catch (ApiException $e) {
            $this->assertSame("service_unavailable", $e->notionCode);
        }

        $this->assertCount(1, $mock);
    }

    public function test_do_not_retry_when_retries_are_disabled(): void
    {
        $mock = new MockHandler([
            new Response(429, ["Retry-After" => "0"], $this->errorJson(429, "rate_limited")),
            new Response(201, [], $this->createdPageJson()),
        ]);
        $config = Configuration::createFromPsrImplementations(
            "secret_123",
            new Client(["handler" => HandlerStack::create($mock)]),
            new HttpFactory(),
        )->withoutRetries();

        $this->expectException(ApiException::class);
        Notion::createFromConfig($config)->pages()->create(Page::create(PageParent::workspace()));
    }

    private function createPage(MockHandler $mock): void
    {
        $this->notion(HandlerStack::create($mock))->pages()->create(Page::create(PageParent::workspace()));
    }

    private function notion(HandlerStack $stack): Notion
    {
        $client = new Client(["handler" => $stack]);
        $policy = RetryPolicy::create(maxRetries: 2, initialDelayMs: 0, maxDelayMs: 0);
        $config = Configuration::createFromPsrImplementations("secret_123", $client, new HttpFactory())
            ->withRetryPolicy($policy);

        return Notion::createFromConfig($config);
    }

    private function errorJson(int $status, string $code, string|null $rateLimitReason = null): string
    {
        $error = [
            "object" => "error",
            "status" => $status,
            "code" => $code,
            "message" => "Error",
        ];
        if ($rateLimitReason !== null) {
            $error["additional_data"] = ["rate_limit_reason" => $rateLimitReason];
        }

        return json_encode($error, JSON_THROW_ON_ERROR);
    }

    private function conflictErrorJson(): string
    {
        return '{
            "object": "error",
            "status": 409,
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
