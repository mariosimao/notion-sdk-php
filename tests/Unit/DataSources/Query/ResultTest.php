<?php

namespace Notion\Test\Unit\DataSources\Query;

use Notion\Common\IncompleteReason;
use Notion\Common\RequestStatusType;
use Notion\DataSources\Query\Result;
use PHPUnit\Framework\TestCase;

class ResultTest extends TestCase
{
    public function test_from_array(): void
    {
        $apiResponse = [
            "results" => [
                [
                    "object" => "page",
                    "id" => "a7e80c0b-a766-43c3-a9e9-21ce94595e0e",
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
                ],
            ],
            "has_more" => true,
            "next_cursor" => "889431ed-4f50-460b-a926-36f6cf0f9669",
        ];

        $result = Result::fromArray($apiResponse);

        $this->assertCount(1, $result->pages);
        $this->assertTrue($result->hasMore);
        $this->assertSame("889431ed-4f50-460b-a926-36f6cf0f9669", $result->nextCursor);
        $this->assertTrue($result->requestStatus->isComplete());
        $this->assertFalse($result->isIncomplete());
    }

    public function test_from_array_with_complete_request_status(): void
    {
        $result = Result::fromArray([
            "results" => [],
            "has_more" => false,
            "next_cursor" => null,
            "request_status" => [ "type" => "complete" ],
        ]);

        $this->assertSame(RequestStatusType::Complete, $result->requestStatus->type);
        $this->assertFalse($result->isIncomplete());
    }

    public function test_from_array_with_incomplete_request_status(): void
    {
        $result = Result::fromArray([
            "results" => [],
            "has_more" => false,
            "next_cursor" => null,
            "request_status" => [
                "type" => "incomplete",
                "incomplete_reason" => "query_result_limit_reached",
            ],
        ]);

        $this->assertFalse($result->hasMore);
        $this->assertTrue($result->isIncomplete());
        $this->assertSame(RequestStatusType::Incomplete, $result->requestStatus->type);
        $this->assertSame(IncompleteReason::QueryResultLimitReached, $result->requestStatus->incompleteReason);
    }
}
