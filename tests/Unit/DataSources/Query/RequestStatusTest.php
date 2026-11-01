<?php

namespace Notion\Test\Unit\DataSources\Query;

use Notion\DataSources\Query\RequestStatus;
use PHPUnit\Framework\TestCase;

class RequestStatusTest extends TestCase
{
    public function test_complete(): void
    {
        $status = RequestStatus::complete();

        $this->assertTrue($status->isComplete());
        $this->assertFalse($status->isIncomplete());
        $this->assertFalse($status->isResultLimitReached());
        $this->assertSame([ "type" => "complete" ], $status->toArray());
    }

    public function test_incomplete(): void
    {
        $status = RequestStatus::incomplete();

        $this->assertFalse($status->isComplete());
        $this->assertTrue($status->isIncomplete());
        $this->assertTrue($status->isResultLimitReached());
        $this->assertSame("query_result_limit_reached", $status->incompleteReason);
    }

    public function test_incomplete_with_other_reason(): void
    {
        $status = RequestStatus::incomplete("other_reason");

        $this->assertTrue($status->isIncomplete());
        $this->assertFalse($status->isResultLimitReached());
    }

    public function test_array_conversion(): void
    {
        $array = [
            "type" => "incomplete",
            "incomplete_reason" => "query_result_limit_reached",
        ];

        $status = RequestStatus::fromArray($array);

        $this->assertSame("incomplete", $status->type);
        $this->assertSame("query_result_limit_reached", $status->incompleteReason);
        $this->assertSame($array, $status->toArray());
    }

    public function test_from_array_without_reason(): void
    {
        $status = RequestStatus::fromArray([ "type" => "complete" ]);

        $this->assertSame("complete", $status->type);
        $this->assertNull($status->incompleteReason);
    }
}
