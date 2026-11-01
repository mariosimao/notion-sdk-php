<?php

namespace Notion\Test\Unit\Sessions;

use Notion\Sessions\ActionOption;
use Notion\Sessions\ActionSubmission;
use Notion\Sessions\RequiredAction;
use PHPUnit\Framework\TestCase;

class ActionSubmissionTest extends TestCase
{
    public function test_submit_actions(): void
    {
        $requiredAction = RequiredAction::fromArray([
            "action_id" => "action-3",
            "title" => "Allow?",
            "options" => [],
        ]);

        $submission = ActionSubmission::create("session-id")
            ->approve("action-1")
            ->reject("action-2")
            ->approve($requiredAction)
            ->answer("action-1", ActionOption::Reject)
            ->changeMetadata([ "source" => "slack" ]);

        $this->assertSame("session-id", $submission->sessionId);
        $this->assertSame([
            "session_id" => "session-id",
            "actions" => [
                [ "action_id" => "action-1", "option_id" => "reject" ],
                [ "action_id" => "action-2", "option_id" => "reject" ],
                [ "action_id" => "action-3", "option_id" => "approve" ],
            ],
            "metadata" => [ "source" => "slack" ],
        ], $submission->toArray());
    }

    public function test_reject_required_action_without_metadata(): void
    {
        $requiredAction = RequiredAction::fromArray([
            "action_id" => "action-1",
            "title" => "Allow?",
            "options" => [ [ "id" => "approve", "label" => "Allow" ] ],
        ]);

        $submission = ActionSubmission::create("session-id")->reject($requiredAction);

        $this->assertSame([
            "session_id" => "session-id",
            "actions" => [ [ "action_id" => "action-1", "option_id" => "reject" ] ],
        ], $submission->toArray());
        $this->assertSame([
            "action_id" => "action-1",
            "title" => "Allow?",
            "options" => [ [ "id" => "approve", "label" => "Allow" ] ],
        ], $requiredAction->toArray());
    }
}
