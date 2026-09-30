<?php

namespace Notion\Test\Unit;

use DateTimeImmutable;
use GuzzleHttp\Psr7\Response;
use Notion\Exceptions\ApiException;
use Notion\Exceptions\RetryPolicyException;
use Notion\RetryPolicy;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class RetryPolicyTest extends TestCase
{
    public function test_default_policy(): void
    {
        $policy = RetryPolicy::create();

        $this->assertSame(2, $policy->maxRetries);
        $this->assertSame(1000, $policy->initialDelayMs);
        $this->assertSame(60000, $policy->maxDelayMs);
    }

    public function test_no_retries(): void
    {
        $policy = RetryPolicy::none();

        $this->assertSame(0, $policy->maxRetries);
        $this->assertFalse($policy->shouldRetry("GET", $this->exception(429, "rate_limited"), 0));
    }

    public function test_negative_max_retries_is_invalid(): void
    {
        $this->expectException(RetryPolicyException::class);
        RetryPolicy::create(maxRetries: -1);
    }

    public function test_negative_initial_delay_is_invalid(): void
    {
        $this->expectException(RetryPolicyException::class);
        RetryPolicy::create(initialDelayMs: -1);
    }

    public function test_max_delay_lower_than_initial_delay_is_invalid(): void
    {
        $this->expectException(RetryPolicyException::class);
        RetryPolicy::create(initialDelayMs: 2000, maxDelayMs: 1000);
    }

    /** @return array<string, array{string, int, string, bool}> */
    public static function retryableProvider(): array
    {
        return [
            "rate limited write"      => ["POST", 429, "rate_limited", true],
            "overloaded write"        => ["PATCH", 529, "service_overload", true],
            "conflict write"          => ["PATCH", 409, "conflict_error", true],
            "server error read"       => ["GET", 500, "internal_server_error", true],
            "bad gateway read"        => ["GET", 502, "", true],
            "unavailable delete"      => ["DELETE", 503, "service_unavailable", true],
            "gateway timeout read"    => ["GET", 504, "", true],
            "server error write"      => ["POST", 500, "internal_server_error", false],
            "unavailable write"       => ["PATCH", 503, "service_unavailable", false],
            "validation error"        => ["GET", 400, "validation_error", false],
            "unauthorized"            => ["GET", 401, "unauthorized", false],
            "not found"               => ["GET", 404, "object_not_found", false],
        ];
    }

    #[DataProvider("retryableProvider")]
    public function test_should_retry(string $method, int $status, string $code, bool $expected): void
    {
        $policy = RetryPolicy::create();

        $this->assertSame($expected, $policy->shouldRetry($method, $this->exception($status, $code), 0));
    }

    public function test_do_not_retry_blocked_requests(): void
    {
        $policy = RetryPolicy::create();
        $exception = $this->exception(429, "rate_limited", "public_api_request_blocked");

        $this->assertFalse($policy->shouldRetry("GET", $exception, 0));
    }

    public function test_do_not_retry_after_max_retries(): void
    {
        $policy = RetryPolicy::create(maxRetries: 2);
        $exception = $this->exception(429, "rate_limited");

        $this->assertTrue($policy->shouldRetry("GET", $exception, 1));
        $this->assertFalse($policy->shouldRetry("GET", $exception, 2));
    }

    public function test_exponential_backoff_with_jitter(): void
    {
        $policy = RetryPolicy::create(initialDelayMs: 1000, maxDelayMs: 60000);
        $response = new Response(529);

        $this->assertThat($policy->delayMs($response, 0), $this->logicalAnd(
            $this->greaterThanOrEqual(1000),
            $this->lessThanOrEqual(1250),
        ));
        $this->assertThat($policy->delayMs($response, 2), $this->logicalAnd(
            $this->greaterThanOrEqual(4000),
            $this->lessThanOrEqual(5000),
        ));
    }

    public function test_backoff_is_capped_by_max_delay(): void
    {
        $policy = RetryPolicy::create(initialDelayMs: 1000, maxDelayMs: 5000);

        $this->assertSame(5000, $policy->delayMs(new Response(529), 10));
    }

    public function test_honor_retry_after_seconds(): void
    {
        $policy = RetryPolicy::create(initialDelayMs: 1000, maxDelayMs: 60000);
        $response = new Response(429, ["Retry-After" => "8"]);

        $this->assertThat($policy->delayMs($response, 0), $this->logicalAnd(
            $this->greaterThanOrEqual(8000),
            $this->lessThanOrEqual(10000),
        ));
    }

    public function test_honor_retry_after_http_date(): void
    {
        $policy = RetryPolicy::create(initialDelayMs: 0, maxDelayMs: 60000);
        $date = (new DateTimeImmutable("+30 seconds"))->format(DATE_RFC7231);
        $response = new Response(429, ["Retry-After" => $date]);

        $this->assertThat($policy->delayMs($response, 0), $this->logicalAnd(
            $this->greaterThanOrEqual(28000),
            $this->lessThanOrEqual(60000),
        ));
    }

    public function test_retry_after_is_capped_by_max_delay(): void
    {
        $policy = RetryPolicy::create(initialDelayMs: 1000, maxDelayMs: 10000);
        $response = new Response(429, ["Retry-After" => "120"]);

        $this->assertSame(10000, $policy->delayMs($response, 0));
    }

    public function test_invalid_retry_after_falls_back_to_backoff(): void
    {
        $policy = RetryPolicy::create(initialDelayMs: 1000, maxDelayMs: 60000);
        $response = new Response(429, ["Retry-After" => "soon"]);

        $this->assertThat($policy->delayMs($response, 0), $this->logicalAnd(
            $this->greaterThanOrEqual(1000),
            $this->lessThanOrEqual(1250),
        ));
    }

    private function exception(int $status, string $code, string|null $rateLimitReason = null): ApiException
    {
        $body = ["object" => "error", "status" => $status, "code" => $code, "message" => "Error"];
        if ($rateLimitReason !== null) {
            $body["additional_data"] = ["rate_limit_reason" => $rateLimitReason];
        }

        return ApiException::fromResponse(new Response($status, [], json_encode($body, JSON_THROW_ON_ERROR)));
    }
}
