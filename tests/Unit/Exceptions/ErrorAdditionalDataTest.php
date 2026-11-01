<?php

namespace Notion\Test\Unit\Exceptions;

use Notion\Exceptions\ErrorAdditionalData;
use PHPUnit\Framework\TestCase;

class ErrorAdditionalDataTest extends TestCase
{
    public function test_empty(): void
    {
        $data = ErrorAdditionalData::empty();

        $this->assertNull($data->retryAfter);
        $this->assertNull($data->rateLimitReason);
        $this->assertSame([], $data->retryGuidance);
        $this->assertNull($data->committedResourceId);
        $this->assertSame([], $data->committedChildIds);
        $this->assertSame([], $data->toArray());
        $this->assertFalse($data->isRequestBlocked());
    }

    public function test_rate_limit_data(): void
    {
        $array = [
            "retry_after" => "42",
            "rate_limit_reason" => "public_api_request_rate_limit",
        ];

        $data = ErrorAdditionalData::fromArray($array);

        $this->assertSame(42, $data->retryAfter);
        $this->assertSame("public_api_request_rate_limit", $data->rateLimitReason);
        $this->assertFalse($data->isRequestBlocked());
        $this->assertSame($array, $data->toArray());
    }

    public function test_integer_retry_after(): void
    {
        $data = ErrorAdditionalData::fromArray([ "retry_after" => 7 ]);

        $this->assertSame(7, $data->retryAfter);
    }

    public function test_invalid_retry_after(): void
    {
        $this->assertNull(ErrorAdditionalData::fromArray([ "retry_after" => "soon" ])->retryAfter);
        $this->assertNull(ErrorAdditionalData::fromArray([ "retry_after" => -1 ])->retryAfter);
        $this->assertNull(ErrorAdditionalData::fromArray([ "retry_after" => "1.5" ])->retryAfter);
    }

    public function test_request_blocked(): void
    {
        $data = ErrorAdditionalData::fromArray([ "rate_limit_reason" => "public_api_request_blocked" ]);

        $this->assertTrue($data->isRequestBlocked());
    }

    public function test_retry_guidance_data(): void
    {
        $array = [
            "from" => "publicApi.pageObjectPointerToPageObject.beforePreload",
            "elapsed_ms" => "55012",
            "deadline_ms" => "55000",
            "retry_guidance" => [
                "Read the object again to confirm the saved change.",
                "Do not repeat the write.",
            ],
            "committed_resource_id" => "ff747ce6-bb89-4c54-80c3-a248a2c78bd9",
            "committed_child_ids" => [ "a", "b" ],
        ];

        $data = ErrorAdditionalData::fromArray($array);

        $this->assertNull($data->retryAfter);
        $this->assertSame($array["retry_guidance"], $data->retryGuidance);
        $this->assertSame("ff747ce6-bb89-4c54-80c3-a248a2c78bd9", $data->committedResourceId);
        $this->assertSame([ "a", "b" ], $data->committedChildIds);
        $this->assertSame("55012", $data->toArray()["elapsed_ms"]);
    }
}
