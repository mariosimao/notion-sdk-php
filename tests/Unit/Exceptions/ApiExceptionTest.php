<?php

namespace Notion\Test\Unit\Exceptions;

use GuzzleHttp\Psr7\Response;
use Notion\Exceptions\ApiException;
use Notion\Exceptions\RateLimitedException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class ApiExceptionTest extends TestCase
{
    public function test_from_response_body(): void
    {
        $body = '{
            "object": "error",
            "status": 404,
            "code": "object_not_found",
            "message": "Could not find page with ID: f077308f-dd9e-4cfe-87e6-7420a8488514."
        }';
        /** @var array{ message: string, code: string } $bodyArray */
        $bodyArray = json_decode($body, true);
        $response = new Response(404, [], $body);

        $e = ApiException::fromResponse($response);

        $this->assertSame($bodyArray["message"], $e->getMessage());
        $this->assertSame($bodyArray["code"], $e->notionCode);
        $this->assertInstanceOf(ResponseInterface::class, $e->response);
    }

    public function test_without_additional_data(): void
    {
        $body = '{ "object": "error", "status": 400, "code": "validation_error", "message": "Invalid" }';
        $e = ApiException::fromResponse(new Response(400, [], $body));

        $this->assertSame([], $e->additionalData->toArray());
        $this->assertNull($e->retryAfter());
    }

    public function test_invalid_body(): void
    {
        $e = ApiException::fromResponse(new Response(502, [], "Bad gateway"));

        $this->assertSame("", $e->notionCode);
        $this->assertSame([], $e->additionalData->toArray());
    }

    public function test_rate_limited_response(): void
    {
        $e = ApiException::fromResponse(new Response(429, [ "Retry-After" => "30" ], $this->rateLimitedJson("30")));

        $this->assertInstanceOf(RateLimitedException::class, $e);
        $this->assertSame("rate_limited", $e->notionCode);
        $this->assertSame(30, $e->additionalData->retryAfter);
        $this->assertSame(30, $e->retryAfter());
        $this->assertSame("public_api_request_rate_limit", $e->rateLimitReason());
        $this->assertTrue($e->isRetryable());
    }

    public function test_retry_after_prefers_body_value(): void
    {
        $e = ApiException::fromResponse(new Response(429, [ "Retry-After" => "60" ], $this->rateLimitedJson("12")));

        $this->assertSame(12, $e->retryAfter());
    }

    public function test_retry_after_falls_back_to_header(): void
    {
        $e = ApiException::fromResponse(new Response(429, [ "Retry-After" => "15" ], $this->rateLimitedJson(null)));

        $this->assertNull($e->additionalData->retryAfter);
        $this->assertSame(15, $e->retryAfter());
    }

    public function test_retry_after_header_as_http_date(): void
    {
        $date = gmdate("D, d M Y H:i:s", time() + 120) . " GMT";
        $e = ApiException::fromResponse(new Response(429, [ "Retry-After" => $date ], $this->rateLimitedJson(null)));

        $retryAfter = $e->retryAfter();
        $this->assertNotNull($retryAfter);
        $this->assertGreaterThanOrEqual(118, $retryAfter);
        $this->assertLessThanOrEqual(120, $retryAfter);
    }

    public function test_invalid_retry_after_header(): void
    {
        $e = ApiException::fromResponse(new Response(429, [ "Retry-After" => "soon" ], $this->rateLimitedJson(null)));

        $this->assertNull($e->retryAfter());
    }

    public function test_blocked_request_is_not_retryable(): void
    {
        $e = ApiException::fromResponse(
            new Response(429, [], $this->rateLimitedJson(null, "public_api_request_blocked")),
        );

        $this->assertInstanceOf(RateLimitedException::class, $e);
        $this->assertFalse($e->isRetryable());
    }

    private function rateLimitedJson(?string $retryAfter, string $reason = "public_api_request_rate_limit"): string
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
}
