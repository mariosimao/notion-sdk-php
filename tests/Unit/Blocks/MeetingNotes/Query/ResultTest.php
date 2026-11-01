<?php

namespace Notion\Test\Unit\Blocks\MeetingNotes\Query;

use Notion\Blocks\MeetingNotes\Query\Result;
use PHPUnit\Framework\TestCase;

class ResultTest extends TestCase
{
    public function test_from_array(): void
    {
        $apiResponse = [
            "results" => [
                [
                    "object"           => "block",
                    "id"               => "04a13895-f072-4814-8af7-cd11af127040",
                    "type"             => "meeting_notes",
                    "meeting_notes"    => [ "is_full_page" => true, "title" => [], "status" => "notes_ready" ],
                    "created_time"     => "2026-01-15T10:00:00.000Z",
                    "last_edited_time" => "2026-01-15T10:30:00.000Z",
                    "created_by"       => [ "object" => "user", "id" => "9b1b1c3e-0f4b-4d2b-8f2e-3f2b25f30a52" ],
                    "last_edited_by"   => [ "object" => "user", "id" => "9b1b1c3e-0f4b-4d2b-8f2e-3f2b25f30a52" ],
                    "has_children"     => true,
                    "in_trash"         => false,
                ],
            ],
            "has_more" => true,
        ];

        $result = Result::fromArray($apiResponse);

        $this->assertCount(1, $result->meetingNotes);
        $this->assertSame("04a13895-f072-4814-8af7-cd11af127040", $result->meetingNotes[0]->metadata()->id);
        $this->assertTrue($result->hasMore);
    }

    public function test_empty_result(): void
    {
        $result = Result::fromArray([ "results" => [], "has_more" => false ]);

        $this->assertSame([], $result->meetingNotes);
        $this->assertFalse($result->hasMore);
    }
}
