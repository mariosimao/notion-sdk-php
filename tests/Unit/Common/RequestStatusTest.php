<?php

namespace Notion\Test\Unit\Common;

use Notion\Common\IncompleteReason;
use Notion\Common\RequestStatus;
use Notion\Common\RequestStatusType;
use PHPUnit\Framework\TestCase;

class RequestStatusTest extends TestCase
{
    public function test_complete(): void
    {
        $status = RequestStatus::complete();

        $this->assertSame(RequestStatusType::Complete, $status->type);
        $this->assertNull($status->incompleteReason);
        $this->assertTrue($status->isComplete());
        $this->assertFalse($status->isIncomplete());
        $this->assertFalse($status->isQueryResultLimitReached());
    }

    public function test_incomplete(): void
    {
        $status = RequestStatus::incomplete(IncompleteReason::QueryResultLimitReached);

        $this->assertSame(RequestStatusType::Incomplete, $status->type);
        $this->assertSame(IncompleteReason::QueryResultLimitReached, $status->incompleteReason);
        $this->assertFalse($status->isComplete());
        $this->assertTrue($status->isIncomplete());
        $this->assertTrue($status->isQueryResultLimitReached());
    }

    public function test_from_array_complete(): void
    {
        $status = RequestStatus::fromArray([ "type" => "complete" ]);

        $this->assertTrue($status->isComplete());
        $this->assertNull($status->incompleteReason);
    }

    public function test_from_array_incomplete(): void
    {
        $status = RequestStatus::fromArray([
            "type" => "incomplete",
            "incomplete_reason" => "query_result_limit_reached",
        ]);

        $this->assertTrue($status->isIncomplete());
        $this->assertTrue($status->isQueryResultLimitReached());
    }

    public function test_from_array_ignores_reason_when_complete(): void
    {
        $status = RequestStatus::fromArray([
            "type" => "complete",
            "incomplete_reason" => "query_result_limit_reached",
        ]);

        $this->assertNull($status->incompleteReason);
    }

    public function test_to_array(): void
    {
        $this->assertSame(
            [ "type" => "complete" ],
            RequestStatus::complete()->toArray(),
        );
        $this->assertSame(
            [ "type" => "incomplete", "incomplete_reason" => "query_result_limit_reached" ],
            RequestStatus::incomplete(IncompleteReason::QueryResultLimitReached)->toArray(),
        );
    }

    public function test_unknown_type_throws(): void
    {
        $this->expectException(\ValueError::class);
        /** @psalm-suppress InvalidArgument */
        RequestStatus::fromArray([ "type" => "unknown" ]);
    }
}
