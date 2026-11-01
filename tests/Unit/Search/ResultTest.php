<?php

namespace Notion\Test\Unit\Search;

use Notion\Common\IncompleteReason;
use Notion\Search\Result;
use PHPUnit\Framework\TestCase;

class ResultTest extends TestCase
{
    public function test_from_array_without_request_status(): void
    {
        $result = Result::fromArray([
            "object" => "list",
            "results" => [],
            "next_cursor" => null,
            "has_more" => false,
        ]);

        $this->assertTrue($result->requestStatus->isComplete());
        $this->assertFalse($result->isIncomplete());
    }

    public function test_from_array_with_incomplete_request_status(): void
    {
        $result = Result::fromArray([
            "object" => "list",
            "results" => [],
            "next_cursor" => null,
            "has_more" => false,
            "request_status" => [
                "type" => "incomplete",
                "incomplete_reason" => "query_result_limit_reached",
            ],
        ]);

        $this->assertTrue($result->isIncomplete());
        $this->assertSame(IncompleteReason::QueryResultLimitReached, $result->requestStatus->incompleteReason);
    }
}
