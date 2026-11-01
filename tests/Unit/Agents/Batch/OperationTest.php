<?php

namespace Notion\Test\Unit\Agents\Batch;

use Notion\Agents\Batch\Action;
use Notion\Agents\Batch\Operation;
use PHPUnit\Framework\TestCase;

class OperationTest extends TestCase
{
    public function test_enable(): void
    {
        $operation = Operation::enable("agent-id");

        $this->assertSame(Action::UpdateStatus, $operation->action);
        $this->assertSame([
            "action" => "update_status",
            "agent_id" => "agent-id",
            "fields" => [ "status" => "active" ],
        ], $operation->toArray());
    }

    public function test_disable(): void
    {
        $this->assertSame([
            "action" => "update_status",
            "agent_id" => "agent-id",
            "fields" => [ "status" => "disabled" ],
        ], Operation::disable("agent-id")->toArray());
    }

    public function test_change_credit_limit(): void
    {
        $this->assertSame([
            "action" => "update_credit_limit",
            "agent_id" => "agent-id",
            "fields" => [ "credit_limit" => 250 ],
        ], Operation::changeCreditLimit("agent-id", 250)->toArray());
    }

    public function test_remove_credit_limit(): void
    {
        $this->assertSame([
            "action" => "update_credit_limit",
            "agent_id" => "agent-id",
            "fields" => [ "credit_limit" => null ],
        ], Operation::removeCreditLimit("agent-id")->toArray());
    }

    public function test_delete(): void
    {
        $this->assertSame([
            "action" => "delete",
            "agent_id" => "agent-id",
        ], Operation::delete("agent-id")->toArray());
    }
}
